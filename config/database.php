<?php

require_once __DIR__ . '/app.php';

$dbHost = (string) env_value('DB_HOST', 'localhost');
$dbHostsRaw = trim((string) env_value('DB_HOSTS', ''));
$dbHosts = [];
if ($dbHostsRaw !== '') {
    foreach (explode(',', $dbHostsRaw) as $candidateHost) {
        $candidateHost = trim($candidateHost);
        if ($candidateHost !== '') {
            $dbHosts[] = $candidateHost;
        }
    }
}

if ($dbHosts === []) {
    $dbHosts = [$dbHost];
}

return [
    'driver' => 'mysql',
    'host' => $dbHost,
    'hosts' => $dbHosts,
    'database' => (string) env_value('DB_NAME', 'website_desa'),
    'username' => (string) env_value('DB_USER', 'root'),
    'password' => (string) env_value('DB_PASS', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
