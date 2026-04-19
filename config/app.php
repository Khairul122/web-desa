<?php

// Deteksi environment dari berbagai sumber
$appEnvFromRuntime = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: '';
if (!$appEnvFromRuntime && defined('APP_ENVIRONMENT')) {
    $appEnvFromRuntime = APP_ENVIRONMENT;
}
$appEnvNormalized = strtolower(trim((string) $appEnvFromRuntime)) ?: 'production';

// Daftar kandidat file konfigurasi rahasia
$secretsCandidates = [
    __DIR__ . '/secrets.example.php', // Default (sebagai referensi struktur)
    __DIR__ . '/secrets.' . $appEnvNormalized . '.php', // Berdasarkan mode (dev/prod)
    __DIR__ . '/secrets.php', // Override lokal (opsional)
];

$loadedSecrets = [];
foreach ($secretsCandidates as $candidateFile) {
    if (is_file($candidateFile)) {
        $secrets = (array) require $candidateFile;
        $loadedSecrets = array_merge($loadedSecrets, $secrets);
    }
}

$GLOBALS['app_secrets'] = $loadedSecrets;

if (!function_exists('env_value')) {
    function env_value(string $key, ?string $default = null): ?string
    {
        $secretValue = $GLOBALS['app_secrets'][$key] ?? null;
        if (is_string($secretValue) && $secretValue !== '') {
            return $secretValue;
        }

        if (is_array($secretValue)) {
            return json_encode($secretValue);
        }

        $serverValue = $_SERVER[$key] ?? null;
        if (is_string($serverValue) && $serverValue !== '') {
            return $serverValue;
        }

        $envValue = $_ENV[$key] ?? null;
        if (is_string($envValue) && $envValue !== '') {
            return $envValue;
        }

        $value = getenv($key);
        if (is_string($value) && $value !== '') {
            return $value;
        }

        return $default;
    }
}

define('ENVIRONMENT', env_value('APP_ENV', 'production'));

define('APP_PATH', __DIR__ . '/../app');
define('CORE_PATH', __DIR__ . '/../core');
define('CONFIG_PATH', __DIR__ . '/../config');
define('ROUTES_PATH', __DIR__ . '/../routes');
define('HELPERS_PATH', __DIR__ . '/../helpers');
define('STORAGE_PATH', __DIR__ . '/../storage');

define('BASE_URL', rtrim((string) env_value('BASE_URL', 'https://munyepirak.wuaze.com'), '/'));
define('PUBLIC_PATH', BASE_URL . '/public');

define('UPLOADS_PATH', BASE_URL . '/uploads');
define('CSS_PATH', PUBLIC_PATH . '/css');
define('JS_PATH', PUBLIC_PATH . '/js');
define('IMAGES_PATH', PUBLIC_PATH . '/images');

define('LOG_VIEWER_TOKEN', (string) env_value('LOG_VIEWER_TOKEN', ''));

$logDir = STORAGE_PATH . '/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}

$appErrorLog = $logDir . '/errors.log';
if (!is_file($appErrorLog)) {
    @touch($appErrorLog);
}

if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', $appErrorLog);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', $appErrorLog);
}
