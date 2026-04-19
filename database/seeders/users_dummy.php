<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("Seeder ini hanya bisa dijalankan lewat CLI.\n");
}

$rootPath = dirname(__DIR__, 2);
$databaseConfigPath = $rootPath . '/config/database.php';

if (!file_exists($databaseConfigPath)) {
    exit("File konfigurasi database tidak ditemukan: {$databaseConfigPath}\n");
}

$config = require $databaseConfigPath;

$required = ['driver', 'host', 'database', 'username', 'password', 'charset', 'options'];
foreach ($required as $key) {
    if (!array_key_exists($key, $config)) {
        exit("Konfigurasi database tidak lengkap. Key '{$key}' tidak ditemukan.\n");
    }
}

$dsn = sprintf(
    '%s:host=%s;dbname=%s;charset=%s',
    $config['driver'],
    $config['host'],
    $config['database'],
    $config['charset']
);

$now = date('Y-m-d H:i:s');
$plainPassword = '12345678';
$passwordHash = password_hash($plainPassword, PASSWORD_BCRYPT);

$users = [
    [
        'username' => 'admin_demo',
        'email' => 'admin@gmail.com',
        'nama_lengkap' => 'Administrator',
        'role' => 'admin',
        'is_active' => 1,
        'created_at' => $now,
    ],
    [
        'username' => 'editor_demo',
        'email' => 'editor_demo@desa.local',
        'nama_lengkap' => 'Editor Demo',
        'role' => 'editor',
        'is_active' => 1,
        'created_at' => $now,
    ],
    [
        'username' => 'author_demo',
        'email' => 'author_demo@desa.local',
        'nama_lengkap' => 'Author Demo',
        'role' => 'author',
        'is_active' => 1,
        'created_at' => $now,
    ],
];

try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

    $deletedCount = (int) $pdo->exec('DELETE FROM users');
    $pdo->exec('ALTER TABLE users AUTO_INCREMENT = 1');

    $stmt = $pdo->prepare(
        'INSERT INTO users (username, password, email, nama_lengkap, role, created_at, is_active)
         VALUES (:username, :password, :email, :nama_lengkap, :role, :created_at, :is_active)'
    );

    $insertedCount = 0;
    foreach ($users as $user) {
        $stmt->execute([
            ':username' => $user['username'],
            ':password' => $passwordHash,
            ':email' => $user['email'],
            ':nama_lengkap' => $user['nama_lengkap'],
            ':role' => $user['role'],
            ':created_at' => $user['created_at'],
            ':is_active' => $user['is_active'],
        ]);
        $insertedCount++;
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    echo "[OK] Seeder users berhasil dijalankan.\n";
    echo "- Data users terhapus: {$deletedCount}\n";
    echo "- Data users dibuat: {$insertedCount}\n";
    echo "\nKredensial dummy (password sama):\n";
    echo "- Username: admin_demo | Password: {$plainPassword} | Role: admin\n";
    echo "- Username: editor_demo | Password: {$plainPassword} | Role: editor\n";
    echo "- Username: author_demo | Password: {$plainPassword} | Role: author\n";
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO) {
        try {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        } catch (Throwable $ignored) {
        }
    }

    fwrite(STDERR, "[ERROR] Seeder users gagal: " . $e->getMessage() . "\n");
    exit(1);
}
