<?php
/**
 * Check every link on the site: all resource links in the database plus the
 * hard-coded links in index.php. Reports broken links and links that redirect
 * somewhere else, so they can be fixed in phpMyAdmin.
 *
 * Usage:
 *   php tools/check_links.php            report problems only
 *   php tools/check_links.php --all      also list links that are fine
 *
 * Exit code is 1 when any link is broken.
 * Needs the curl extension (included with XAMPP's command-line PHP).
 *
 * Results are a guide, not a verdict: some sites block automated requests
 * (often 403/429) but work in a browser. Open those by hand.
 */
declare(strict_types=1);

require __DIR__ . '/cli.php';

if (!extension_loaded('curl')) {
    fail('the curl extension is not loaded');
}

$showAll = in_array('--all', $argv, true);

// Quick mode: php tools/check_links.php https://a.example https://b.example
// Fetches just those URLs and prints status, final URL and page title.
$explicit = array_values(array_filter(array_slice($argv, 1), static fn (string $a): bool => preg_match('~^https?://~i', $a) === 1));
if ($explicit !== []) {
    foreach ($explicit as $url) {
        $ch = make_handle($url, false);
        $body = (string) curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $final = (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $error = curl_error($ch);
        $title = preg_match('~<title[^>]*>(.*?)</title>~is', $body, $t) === 1
            ? trim((string) preg_replace('/\s+/', ' ', html_entity_decode($t[1], ENT_QUOTES | ENT_HTML5, 'UTF-8')))
            : '';
        out($url);
        out('    ' . ($error !== '' ? "error: {$error}" : "HTTP {$code}") . ($final !== $url ? "  -> {$final}" : ''));
        out('    title: ' . ($title !== '' ? $title : '(none)'));
    }
    exit(0);
}

// ---- Collect links --------------------------------------------------------
/** @var array<string, list<string>> $links  url => where it is used */
$links = [];

try {
    $rows = db()->query(
        'SELECT r.id, r.name, r.link, s.slug, s.lang
           FROM resources r JOIN sections s ON s.id = r.section_id
          ORDER BY s.sort_order, s.lang, r.sort_order, r.id'
    )->fetchAll();
} catch (Throwable $e) {
    fail('could not read the database: ' . $e->getMessage());
}
foreach ($rows as $row) {
    $links[trim((string) $row['link'])][] = sprintf('resource #%d %s/%s "%s"', $row['id'], $row['slug'], $row['lang'], $row['name']);
}

$page = (string) file_get_contents(dirname(__DIR__) . '/index.php');
preg_match_all('~href="(https?://[^"]+)"~', $page, $m);
foreach (array_unique($m[1]) as $url) {
    $links[html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8')][] = 'index.php (static text)';
}

out(sprintf('checking %d unique links...', count($links)));

// ---- Fetch in parallel ----------------------------------------------------
function make_handle(string $url, bool $head): CurlHandle
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_NOBODY         => $head,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_ENCODING       => '',
        // Some sites refuse requests that don't look like a browser.
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0 Safari/537.36 GAL-link-check',
        CURLOPT_HTTPHEADER     => ['Accept: text/html,application/xhtml+xml,*/*;q=0.8', 'Accept-Language: en-US,en;q=0.8,es;q=0.6'],
    ]);
    return $ch;
}

/**
 * @param list<string> $urls
 * @return array<string, array{code: int, final: string, error: string}>
 */
function fetch_all(array $urls, bool $head, int $parallel = 8): array
{
    $results = [];
    $queue = $urls;
    $mh = curl_multi_init();
    $active = [];

    $add = static function () use (&$queue, &$active, $mh, $head, $parallel): void {
        while (count($active) < $parallel && $queue !== []) {
            $url = array_shift($queue);
            $ch = make_handle($url, $head);
            curl_multi_add_handle($mh, $ch);
            $active[spl_object_id($ch)] = [$ch, $url];
        }
    };

    $add();
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh, 1.0);
        while ($info = curl_multi_info_read($mh)) {
            $ch = $info['handle'];
            [, $url] = $active[spl_object_id($ch)];
            $results[$url] = [
                'code'  => (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE),
                'final' => (string) curl_getinfo($ch, CURLINFO_EFFECTIVE_URL),
                'error' => $info['result'] === CURLE_OK ? '' : curl_error($ch),
            ];
            curl_multi_remove_handle($mh, $ch);
            unset($active[spl_object_id($ch)]);
            $add();
        }
    } while ($running > 0 || $active !== []);
    curl_multi_close($mh);
    return $results;
}

$urls = array_keys($links);
$results = fetch_all($urls, true);

// Many servers answer HEAD badly (403/404/405/5xx); retry those with GET.
$retry = array_keys(array_filter($results, static fn (array $r): bool => $r['code'] === 0 || $r['code'] >= 400));
if ($retry !== []) {
    $results = array_merge($results, fetch_all($retry, false));
}

// ---- Report ---------------------------------------------------------------
/** Normalise a URL for "did it really move?" comparison. */
function normalise(string $url): string
{
    $url = strtolower(rtrim($url, '/'));
    return (string) preg_replace('~^https?://(www\.)?~', '', $url);
}

$groups = ['BROKEN' => [], 'BLOCKED?' => [], 'MOVED' => [], 'HTTPS' => [], 'OK' => []];
foreach ($urls as $url) {
    $r = $results[$url];
    $where = $links[$url];
    if ($r['error'] !== '' || $r['code'] === 0) {
        $groups['BROKEN'][] = [$url, 'error: ' . ($r['error'] ?: 'no response'), $where];
    } elseif (in_array($r['code'], [401, 403, 429, 503], true)) {
        $groups['BLOCKED?'][] = [$url, "HTTP {$r['code']} (may only block robots; check in a browser)", $where];
    } elseif ($r['code'] >= 400) {
        $groups['BROKEN'][] = [$url, "HTTP {$r['code']}", $where];
    } elseif (normalise($r['final']) !== normalise($url)) {
        $groups['MOVED'][] = [$url, "now at {$r['final']}", $where];
    } elseif (str_starts_with($url, 'http://') && str_starts_with($r['final'], 'https://')) {
        $groups['HTTPS'][] = [$url, 'use https://', $where];
    } else {
        $groups['OK'][] = [$url, "HTTP {$r['code']}", $where];
    }
}

foreach ($groups as $label => $items) {
    if ($items === [] || ($label === 'OK' && !$showAll)) {
        continue;
    }
    out();
    out("== {$label} (" . count($items) . ')');
    foreach ($items as [$url, $note, $where]) {
        out("  {$url}");
        out("      {$note}");
        foreach ($where as $w) {
            out("      used by {$w}");
        }
    }
}

out();
out(implode(', ', array_map(static fn (string $k, array $v): string => count($v) . ' ' . strtolower($k), array_keys($groups), $groups)));
exit($groups['BROKEN'] === [] ? 0 : 1);
