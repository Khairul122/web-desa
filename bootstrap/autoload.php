<?php

require_once __DIR__ . '/../config/app.php';

spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\' => APP_PATH . '/',
        'App\\Core\\' => CORE_PATH . '/',
        'App\\Database\\' => dirname(__DIR__) . '/core/',
        'App\\Http\\' => dirname(__DIR__) . '/core/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

require_once HELPERS_PATH . '/functions.php';
