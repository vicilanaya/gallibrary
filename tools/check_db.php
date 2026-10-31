<?php
/**
 * Check the database connection and list tables with row counts.
 *
 * Usage: php tools/check_db.php
 */
declare(strict_types=1);

require __DIR__ . '/cli.php';

try {
    $pdo = db();
} catch (Throwable $e) {
    fail('could not connect: ' . $e->getMessage());
}

$version = (string) $pdo->query('SELECT VERSION()')->fetchColumn();
$dbName  = (string) $pdo->query('SELECT DATABASE()')->fetchColumn();
out("connected to '{$dbName}' (server {$version})");

$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
if ($tables === []) {
    out('no tables yet - import schema.sql');
    exit(0);
}

foreach ($tables as $table) {
    // Table names come from SHOW TABLES, quoted as identifiers.
    $count = (int) $pdo->query('SELECT COUNT(*) FROM `' . str_replace('`', '``', $table) . '`')->fetchColumn();
    out(sprintf('  %-12s %d rows', $table, $count));
}
