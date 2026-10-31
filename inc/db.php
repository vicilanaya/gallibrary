<?php
/**
 * Database access for the GAL Resource Library.
 * Compatible with PHP 8.3 (InfinityFree).
 */
declare(strict_types=1);

/**
 * Load config.php from the project root.
 *
 * @return array{db: array{host: string, port?: int, name: string, user: string, password: string}}
 */
function load_config(): array
{
    $file = dirname(__DIR__) . '/config.php';
    if (!is_file($file)) {
        throw new RuntimeException('config.php not found. Copy config.sample.php to config.php and fill in the database settings.');
    }
    $config = require $file;
    if (!is_array($config) || !isset($config['db']) || !is_array($config['db'])) {
        throw new RuntimeException('config.php must return an array with a "db" key.');
    }
    return $config;
}

/**
 * Shared PDO connection (created once per request).
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $db = load_config()['db'];
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $db['host'],
        (int) ($db['port'] ?? 3306),
        $db['name']
    );

    $pdo = new PDO($dsn, $db['user'], $db['password'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

/**
 * All sections in page order (English row before Spanish row for each slug).
 *
 * @return list<array{id: int, slug: string, lang: string, name: string, sort_order: int}>
 */
function get_sections(PDO $pdo): array
{
    $rows = $pdo->query(
        'SELECT id, slug, lang, name, sort_order
           FROM sections
          ORDER BY sort_order, lang, id'
    )->fetchAll();

    foreach ($rows as &$row) {
        $row['id'] = (int) $row['id'];
        $row['sort_order'] = (int) $row['sort_order'];
    }
    unset($row);
    return $rows;
}

/**
 * All resources grouped by section id, each group in display order.
 *
 * @return array<int, list<array{id: int, name: string, link: string, description: string}>>
 */
function get_resources_by_section(PDO $pdo): array
{
    $rows = $pdo->query(
        'SELECT id, section_id, name, link, description
           FROM resources
          ORDER BY section_id, sort_order, id'
    )->fetchAll();

    $grouped = [];
    foreach ($rows as $row) {
        $grouped[(int) $row['section_id']][] = [
            'id'          => (int) $row['id'],
            'name'        => (string) $row['name'],
            'link'        => (string) $row['link'],
            'description' => (string) ($row['description'] ?? ''),
        ];
    }
    return $grouped;
}
