<?php
/**
 * Plain-PHP test runner (no framework needed).
 *
 * Usage: php tests/run_tests.php
 *
 * Needs the local database imported from resources.xml (tools/import_xml.php).
 * Read-only: it never changes the database.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
require_once $root . '/inc/db.php';
require_once $root . '/inc/html.php';

$passed = 0;
$failed = 0;

function check(bool $condition, string $label): void
{
    global $passed, $failed;
    if ($condition) {
        $passed++;
    } else {
        $failed++;
        fwrite(STDOUT, "  FAIL: {$label}" . PHP_EOL);
    }
}

function group(string $title): void
{
    fwrite(STDOUT, "- {$title}" . PHP_EOL);
}

// ---------------------------------------------------------------------------
group('helpers');
check(h('Women, Infants & Children') === 'Women, Infants &amp; Children', 'h() escapes &');
check(h('<b>"x"</b> \'y\'') === '&lt;b&gt;&quot;x&quot;&lt;/b&gt; &apos;y&apos;', 'h() escapes < > " \'');
check(h(null) === '', 'h(null) is empty');
check(h('Educación ’ñ') === 'Educación ’ñ', 'h() keeps UTF-8 text');
check(safe_url('https://example.org/a?b=1') === 'https://example.org/a?b=1', 'safe_url allows https');
check(safe_url('http://example.org') === 'http://example.org', 'safe_url allows http');
check(safe_url('mailto:a@example.org') === 'mailto:a@example.org', 'safe_url allows mailto');
check(safe_url('tel:407-555-0100') === 'tel:407-555-0100', 'safe_url allows tel');
check(safe_url('javascript:alert(1)') === '#', 'safe_url blocks javascript:');
check(safe_url(' JavaScript:alert(1)') === '#', 'safe_url blocks mixed-case javascript:');
check(safe_url('data:text/html,x') === '#', 'safe_url blocks data:');
check(section_anchor('education', 'en') === 'education', 'English anchor is the slug');
check(section_anchor('education', 'es') === 'education-es', 'Spanish anchor is slug-es');

// ---------------------------------------------------------------------------
group('database matches resources.xml');
$xml = simplexml_load_file($root . '/resources.xml', SimpleXMLElement::class, LIBXML_NONET);
check($xml !== false, 'resources.xml parses');

$pdo       = db();
$sections  = get_sections($pdo);
$resources = get_resources_by_section($pdo);

$xmlSections = $xml->xpath('/resources/section');
$xmlResources = $xml->xpath('/resources/section/resource');
$dbResourceTotal = array_sum(array_map('count', $resources));
check(count($sections) === count($xmlSections), sprintf('section count: db %d, xml %d', count($sections), count($xmlSections)));
check($dbResourceTotal === count($xmlResources), sprintf('resource count: db %d, xml %d', $dbResourceTotal, count($xmlResources)));

// Same order, names, links and descriptions, row by row.
foreach ($xmlSections as $i => $xs) {
    $slug = (string) $xs['section'];
    $lang = (string) $xs->lang;
    $db   = $sections[$i] ?? null;
    $where = "section #{$i} ({$slug}/{$lang})";
    check($db !== null && $db['slug'] === $slug && $db['lang'] === $lang, "{$where}: same slug, lang and position");
    if ($db === null) {
        continue;
    }
    check($db['name'] === trim((string) $xs->sectionname), "{$where}: same name");

    $dbRows = $resources[$db['id']] ?? [];
    check(count($dbRows) === count($xs->resource), "{$where}: same resource count");
    // SimpleXML iteration keys are element names, so track the index by hand.
    $j = 0;
    foreach ($xs->resource as $xr) {
        $row = $dbRows[$j] ?? null;
        check(
            $row !== null
            && $row['name'] === trim((string) $xr->name)
            && $row['link'] === trim((string) $xr->link)
            && $row['description'] === trim((string) $xr->description),
            "{$where}: resource #{$j} identical"
        );
        $j++;
    }
}

// Language split and ordering: each slug has en then es, sharing sort_order.
$bySlug = [];
foreach ($sections as $s) {
    $bySlug[$s['slug']][] = $s;
}
foreach ($bySlug as $slug => $pair) {
    check(array_column($pair, 'lang') === ['en', 'es'], "{$slug}: en row then es row");
    check(count(array_unique(array_column($pair, 'sort_order'))) === 1, "{$slug}: en and es share sort_order");
}

// Spot checks for special characters.
$allRows = array_merge(...array_values($resources));
$names = array_column($allRows, 'name');
$descriptions = array_column($allRows, 'description');
check(in_array('Women, Infants & Children (WIC)', $names, true), '& stored as a plain character (WIC)');
check((bool) array_filter($descriptions, static fn (string $d): bool => str_contains($d, 'Daniel’s quality')), 'curly apostrophe stored (Daniel’s)');
check(in_array('Educación', array_column($sections, 'name'), true), 'accented section name stored (Educación)');

$xmlEmpty = count(array_filter($xmlResources, static fn ($r): bool => trim((string) $r->description) === ''));
$dbEmpty  = count(array_filter($descriptions, static fn (string $d): bool => $d === ''));
check($xmlEmpty === $dbEmpty, "empty descriptions: db {$dbEmpty}, xml {$xmlEmpty}");
fwrite(STDOUT, "  ({$dbEmpty} empty descriptions)" . PHP_EOL);

// Some links differ between languages; make sure they were not merged.
$fafsa = array_values(array_filter($allRows, static fn (array $r): bool => $r['name'] === 'FAFSA'));
check(count($fafsa) === 2 && $fafsa[0]['link'] !== $fafsa[1]['link'], 'per-language links kept (FAFSA en/es differ)');

// ---------------------------------------------------------------------------
group('rendered index.php');
$html = (static function () use ($root): string {
    ob_start();
    include $root . '/index.php';
    return (string) ob_get_clean();
})();

check(str_starts_with(ltrim($html), '<!DOCTYPE html>'), 'starts with <!DOCTYPE html>');
check(!str_contains($html, 'xsl'), 'no XSLT references');
check(str_contains($html, 'Women, Infants &amp; Children (WIC)'), '& escaped in output');
check(!str_contains($html, '<?php'), 'no raw PHP tags in output');

libxml_use_internal_errors(true);   // DOMDocument warns about HTML5 tags
$dom = new DOMDocument();
$dom->loadHTML('<?xml encoding="utf-8"?>' . $html);
libxml_clear_errors();
$xp = new DOMXPath($dom);

$navItems = $xp->query('//nav/ul/li');
check($navItems->length === count($sections), "nav has one item per section ({$navItems->length})");

$headings = $xp->query('//h2[@id]');
check($headings->length === count($sections), "one heading with id per section ({$headings->length})");

$ids = [];
foreach ($xp->query('//*[@id]') as $el) {
    $ids[] = $el->getAttribute('id');
}
check(count($ids) === count(array_unique($ids)), 'all ids are unique');
foreach ($ids as $id) {
    if (preg_match('/^[a-z0-9-]+$/i', $id) !== 1) {
        check(false, "id is ASCII without spaces: '{$id}'");
    }
}

$dts = $xp->query('//dl/dt/a[@target="_blank"]');
check($dts->length === $dbResourceTotal, "one link per resource ({$dts->length})");
$dds = $xp->query('//dl/dd');
check($dds->length === $dbResourceTotal, 'every <dl> has a <dd> (valid HTML)');

// Every in-page link (#...) points at an existing id.
foreach ($xp->query('//a[starts-with(@href, "#")]') as $a) {
    $target = substr($a->getAttribute('href'), 1);
    check(in_array($target, $ids, true), "in-page link #{$target} has a target");
}

// Old anchors are kept as data-legacy-id for resources.js.
$legacy = [];
foreach ($xp->query('//h2[@data-legacy-id]') as $el) {
    $legacy[] = $el->getAttribute('data-legacy-id');
}
foreach (['Education', 'Educación', 'Employment', 'Empleo', 'Travel', 'Viajar', 'Social Services'] as $old) {
    check(in_array($old, $legacy, true), "legacy anchor '{$old}' available");
}

// No <ul> directly inside a <ul>.
check($xp->query('//ul/ul')->length === 0, 'no <ul> directly inside <ul>');

// Nav and sections carry the right lang so the EN/ES toggle works.
$langs = array_count_values(array_map(static fn (DOMElement $e): string => $e->getAttribute('lang'), iterator_to_array($xp->query('//nav/ul/li'))));
check(($langs['en'] ?? 0) === count($bySlug) && ($langs['es'] ?? 0) === count($bySlug), 'nav items split evenly en/es');
check($xp->query('/html[@lang]')->length === 0, '<html> has no lang (the jQuery toggle would hide it)');

// ---------------------------------------------------------------------------
fwrite(STDOUT, PHP_EOL . "{$passed} passed, {$failed} failed" . PHP_EOL);
exit($failed === 0 ? 0 : 1);
