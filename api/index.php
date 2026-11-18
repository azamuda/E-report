<?php

/**
 * Vercel Serverless Entrypoint for Laravel
 */

// Siapkan folder writable di /tmp karena serverless AWS/Vercel read-only
$tmpDirectories = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirectories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Redirect view compiler ke /tmp
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Jalankan aplikasi Laravel via public/index.php
require __DIR__ . '/../public/index.php';
