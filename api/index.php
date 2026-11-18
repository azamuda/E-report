<?php

/**
 * Vercel Serverless Entrypoint for Laravel
 */

// Siapkan folder writable di /tmp karena serverless AWS Lambda / Vercel read-only
$tmpDirectories = [
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
];

foreach ($tmpDirectories as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Salin cached packages dan services jika ada ke /tmp
foreach (['packages.php', 'services.php'] as $cacheFile) {
    $src = __DIR__.'/../bootstrap/cache/'.$cacheFile;
    $dest = '/tmp/bootstrap/cache/'.$cacheFile;
    if (file_exists($src) && ! file_exists($dest)) {
        copy($src, $dest);
    }
}

// Set environment variables penting agar Laravel menggunakan /tmp
$envOverrides = [
    'LARAVEL_STORAGE_PATH' => '/tmp/storage',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
    'APP_PACKAGES_CACHE' => '/tmp/bootstrap/cache/packages.php',
    'APP_SERVICES_CACHE' => '/tmp/bootstrap/cache/services.php',
    'APP_CONFIG_CACHE' => '/tmp/bootstrap/cache/config.php',
    'APP_ROUTES_CACHE' => '/tmp/bootstrap/cache/routes-v7.php',
    'APP_EVENTS_CACHE' => '/tmp/bootstrap/cache/events.php',
];

foreach ($envOverrides as $key => $val) {
    putenv("{$key}={$val}");
    $_ENV[$key] = $val;
    $_SERVER[$key] = $val;
}

// Jalankan aplikasi Laravel via public/index.php
require __DIR__.'/../public/index.php';
