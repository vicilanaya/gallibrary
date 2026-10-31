<?php
/**
 * Export the sections and resources rows as plain INSERT statements, for
 * importing into the InfinityFree database with phpMyAdmin (after schema.sql).
 *
 * A custom exporter is used instead of mysqldump because recent MariaDB
 * mysqldump versions add a "sandbox mode" first line that MySQL rejects, and
 * dumps can include LOCK TABLES / CREATE DATABASE statements that shared
 * hosting doesn't allow.
 *
 * Usage: php tools/export_sql.php [output-file]   (default: data.sql)
 */
declare(strict_types=1);

require __DIR__ . '/cli.php';

$outFile = $argv[1] ?? dirname(__DIR__) . '/data.sql';

try {
    $pdo = db();
} catch (Throwable $e) {
    fail('could not connect: ' . $e->getMessage());
}

/**
 * Build multi-row INSERT statements for a table.
 *
 * @param list<string> $columns
 */
function insert_statements(PDO $pdo, string $table, array $columns, int $batch = 50): string
{
    $colList = implode(', ', $columns);
    $rows = $pdo->query("SELECT {$colList} FROM {$table} ORDER BY id")->fetchAll(PDO::FETCH_NUM);
    if ($rows === []) {
        return "-- {$table}: no rows\n";
    }

    $sql = '';
    foreach (array_chunk($rows, $batch) as $chunk) {
        $values = [];
        foreach ($chunk as $row) {
            $values[] = '(' . implode(', ', array_map(
                static fn (mixed $v): string => $v === null ? 'NULL' : (is_int($v) ? (string) $v : $pdo->quote((string) $v)),
                $row
            )) . ')';
        }
        $sql .= "INSERT INTO {$table} ({$colList}) VALUES\n" . implode(",\n", $values) . ";\n";
    }
    return $sql;
}

$sections  = insert_statements($pdo, 'sections', ['id', 'slug', 'lang', 'name', 'sort_order']);
$resources = insert_statements($pdo, 'resources', ['id', 'section_id', 'name', 'link', 'description', 'sort_order']);

$sql = "-- GAL Resource Library data, exported " . gmdate('Y-m-d H:i') . " UTC\n"
     . "-- Import AFTER schema.sql, into empty tables.\n\n"
     . "SET NAMES utf8mb4;\n"
     . "START TRANSACTION;\n\n"
     . $sections . "\n"
     . $resources . "\n"
     . "COMMIT;\n";

if (file_put_contents($outFile, $sql) === false) {
    fail("could not write {$outFile}");
}

$sectionCount  = (int) $pdo->query('SELECT COUNT(*) FROM sections')->fetchColumn();
$resourceCount = (int) $pdo->query('SELECT COUNT(*) FROM resources')->fetchColumn();
out("wrote {$outFile} ({$sectionCount} sections, {$resourceCount} resources)");
