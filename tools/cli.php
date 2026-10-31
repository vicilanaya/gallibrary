<?php
/**
 * Shared bootstrap for command-line tools. Refuses to run from a web request.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/inc/db.php';

/**
 * Print a line to stdout.
 */
function out(string $line = ''): void
{
    fwrite(STDOUT, $line . PHP_EOL);
}

/**
 * Print an error to stderr and exit with a non-zero code.
 */
function fail(string $message): never
{
    fwrite(STDERR, 'ERROR: ' . $message . PHP_EOL);
    exit(1);
}
