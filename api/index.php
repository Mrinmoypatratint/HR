<?php

/**
 * Vercel Serverless Entrypoint for Laravel 11
 * Handles AWS Lambda /tmp writable requirements for cache, sessions, and SQLite.
 */

// Ensure writable storage directories in /tmp
$dirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Prepare SQLite database in /tmp if running SQLite in serverless
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    $templateDb = __DIR__ . '/../database/seed_template.db';
    if (file_exists($templateDb) && filesize($templateDb) > 0) {
        @copy($templateDb, $dbPath);
    } else {
        @touch($dbPath);
    }
}

putenv("DB_DATABASE={$dbPath}");
$_ENV['DB_DATABASE'] = $dbPath;

// Route the request through Laravel's public front controller
require __DIR__ . '/../public/index.php';
