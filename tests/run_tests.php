<?php
/**
 * Plain-PHP test runner (no framework needed).
 *
 * Usage:
 *   php tests/run_tests.php         helpers, database integrity, rendered page, CSS/JS
 *   php tests/run_tests.php --xml   also compare the database row-by-row with
 *                                   resources.xml (only right straight after
 *                                   tools/import_xml.php; the database has
 *                                   since been updated on purpose)
 *
 * Needs the local database. Read-only: it never changes the database.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
require_once $root . '/inc/db.php';
require_once $root . '/inc/html.php';

$compareXml = in_array('--xml', $argv, true);
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

/**
 * Render index.php with the given query parameters and return the HTML.
 *
 * @param array<string, mixed> $get
 */
function render_page(string $root, array $get = []): string
{
    $_GET = $get;
    ob_start();
    include $root . '/index.php';
    $_GET = [];
    return (string) ob_get_clean();
}

function parse_html(string $html): DOMXPath
{
    libxml_use_internal_errors(true);   // DOMDocument warns about HTML5 tags
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8"?>' . $html);
    libxml_clear_errors();
    return new DOMXPath($dom);
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
group('database integrity');
$pdo       = db();
$sections  = get_sections($pdo);
$resources = get_resources_by_section($pdo);
$allRows   = $resources === [] ? [] : array_merge(...array_values($resources));
$dbResourceTotal = count($allRows);

check(count($sections) > 0, 'has sections');
check($dbResourceTotal > 0, 'has resources');

$bySlug = [];
foreach ($sections as $s) {
    check(preg_match('/^[a-z0-9-]{1,50}$/', $s['slug']) === 1, "slug '{$s['slug']}' is lowercase a-z, 0-9, -");
    $bySlug[$s['slug']][] = $s;
}
foreach ($bySlug as $slug => $pair) {
    check(array_column($pair, 'lang') === ['en', 'es'], "{$slug}: en row then es row");
    check(count(array_unique(array_column($pair, 'sort_order'))) === 1, "{$slug}: en and es share sort_order");
}

foreach ($allRows as $row) {
    check(safe_url($row['link']) !== '#', "resource #{$row['id']} link starts with http(s)://: {$row['link']}");
}

$names = array_column($allRows, 'name');
$descriptions = array_column($allRows, 'description');
$links = array_column($allRows, 'link');
check(in_array('Women, Infants & Children (WIC)', $names, true), '& stored as a plain character (WIC)');
check(in_array('Educación', array_column($sections, 'name'), true), 'accented section name stored (Educación)');

// 2026-11-01 link update (updates/2026-11-01-links.sql)
$allText = implode("\n", array_merge($names, $descriptions, $links));
check(!str_contains($allText, '273-8255') && !str_contains($allText, '628-9454'), 'no retired suicide-lifeline numbers');
check(!str_contains($allText, 'suicidepreventionlifeline.org'), 'no old lifeline website');
check(!str_contains($allText, 'myfloridapd.com'), 'no hijacked public defender domain');
check(in_array('https://988lifeline.org/', $links, true) && in_array('https://988lifeline.org/es/inicio/', $links, true), '988 Lifeline linked in both languages');
check(in_array('https://pd9.org/', $links, true), 'public defender links to pd9.org');

$fafsa = array_values(array_filter($allRows, static fn (array $r): bool => $r['name'] === 'FAFSA'));
check(count($fafsa) === 2 && $fafsa[0]['link'] !== $fafsa[1]['link'], 'per-language links kept (FAFSA en/es differ)');

// ---------------------------------------------------------------------------
if ($compareXml) {
    group('database matches resources.xml (--xml)');
    $xml = simplexml_load_file($root . '/resources.xml', SimpleXMLElement::class, LIBXML_NONET);
    check($xml !== false, 'resources.xml parses');
    $xmlSections = $xml->xpath('/resources/section');
    $xmlResources = $xml->xpath('/resources/section/resource');
    check(count($sections) === count($xmlSections), sprintf('section count: db %d, xml %d', count($sections), count($xmlSections)));
    check($dbResourceTotal === count($xmlResources), sprintf('resource count: db %d, xml %d', $dbResourceTotal, count($xmlResources)));

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
}

// ---------------------------------------------------------------------------
group('rendered index.php (English default)');
$html = render_page($root);
$xp = parse_html($html);

check(str_starts_with(ltrim($html), '<!DOCTYPE html>'), 'starts with <!DOCTYPE html>');
check(!str_contains($html, 'xsl'), 'no XSLT references');
check(!str_contains(strtolower($html), 'jquery'), 'no jQuery');
check(str_contains($html, 'Women, Infants &amp; Children (WIC)'), '& escaped in output');
check(!str_contains($html, '<?php'), 'no raw PHP tags in output');
check(!str_contains($html, 'nationalservice.gov') && !str_contains($html, 'goo.gl'), 'outdated static links replaced');
check(str_contains($html, 'favicon-large-white.ico'), 'favicon linked');
$cssV = 'resources.css?v=' . filemtime($root . '/resources.css');
$jsV  = 'resources.js?v=' . filemtime($root . '/resources.js');
check(str_contains($html, 'href="' . $cssV . '"'), "stylesheet has a version number ({$cssV})");
check(str_contains($html, 'src="' . $jsV . '"'), "script has a version number ({$jsV})");

check($xp->query('/html[@lang="en"]')->length === 1, '<html lang="en">');
check($xp->query('/html[contains(@class, "show-es")]')->length === 0, 'no show-es class by default');

$robots = $xp->query('//meta[@name="robots"]');
check($robots->length === 1 && $robots->item(0)->getAttribute('content') === 'index, follow', 'one valid robots meta tag');
check($xp->query('//ul/br')->length === 0, 'no <br> directly inside <ul>');
check($xp->query('//ul/ul')->length === 0, 'no <ul> directly inside <ul>');

foreach (['english' => 'true', 'spanish' => 'false'] as $id => $pressed) {
    $b = $xp->query("//button[@id='{$id}']");
    check($b->length === 1, "#{$id} is a <button>");
    if ($b->length === 1) {
        check($b->item(0)->getAttribute('type') === 'button', "#{$id} has type=button");
        check($b->item(0)->getAttribute('aria-pressed') === $pressed, "#{$id} aria-pressed={$pressed}");
        check(str_contains($b->item(0)->getAttribute('class'), 'lang-switch'), "#{$id} has class lang-switch (stays visible)");
    }
}
check($xp->query("//button[@id='print' and @type='button']")->length === 1, '#print is a <button>');

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
check($xp->query('//dl/dd')->length === $dbResourceTotal, 'every <dl> has a <dd> (valid HTML)');

foreach ($xp->query('//a[starts-with(@href, "#")]') as $a) {
    $target = substr($a->getAttribute('href'), 1);
    check(in_array($target, $ids, true), "in-page link #{$target} has a target");
}

$legacy = [];
foreach ($xp->query('//h2[@data-legacy-id]') as $el) {
    $legacy[] = $el->getAttribute('data-legacy-id');
}
foreach (['Education', 'Educación', 'Employment', 'Empleo', 'Travel', 'Viajar', 'Social Services'] as $old) {
    check(in_array($old, $legacy, true), "legacy anchor '{$old}' available");
}

$langs = array_count_values(array_map(static fn (DOMElement $e): string => $e->getAttribute('lang'), iterator_to_array($navItems)));
check(($langs['en'] ?? 0) === count($bySlug) && ($langs['es'] ?? 0) === count($bySlug), 'nav items split evenly en/es');

// ---------------------------------------------------------------------------
group('rendered index.php (?lang=es)');
$xpEs = parse_html(render_page($root, ['lang' => 'es']));
check($xpEs->query('/html[@lang="es" and contains(@class, "show-es")]')->length === 1, '<html lang="es" class="show-es">');
check($xpEs->query("//button[@id='spanish' and @aria-pressed='true']")->length === 1, '#spanish aria-pressed=true');
check($xpEs->query("//button[@id='english' and @aria-pressed='false']")->length === 1, '#english aria-pressed=false');

$xpOdd = parse_html(render_page($root, ['lang' => ['es']]));
check($xpOdd->query('/html[@lang="en"]')->length === 1, '?lang[]=es (array) falls back to English');
$xpOdd = parse_html(render_page($root, ['lang' => '<script>']));
check($xpOdd->query('/html[@lang="en"]')->length === 1, '?lang=<script> falls back to English');

// ---------------------------------------------------------------------------
group('resources.css and resources.js');
$css = (string) file_get_contents($root . '/resources.css');
$js  = (string) file_get_contents($root . '/resources.js');
check(!str_contains($css, 'san-serif'), 'font fallback spelled sans-serif');
check(str_contains($css, '@media (max-width: 700px)'), 'has a narrow-screen layout');
check(str_contains($css, '@media print'), 'has a print stylesheet');
check(str_contains($css, 'scroll-margin-top'), 'headings clear the sticky bar');
check(str_contains($css, 'html.show-es body [lang="en"]:not(.lang-switch)'), 'language hiding keyed on <html> class');
check(!str_contains($js, '$(') && !str_contains($js, 'jQuery'), 'resources.js does not use jQuery');
check(str_contains($js, 'localStorage'), 'resources.js remembers the language');

// ---------------------------------------------------------------------------
fwrite(STDOUT, PHP_EOL . "{$passed} passed, {$failed} failed" . PHP_EOL);
exit($failed === 0 ? 0 : 1);
