<?php

namespace App\Models;

use App\Core\Database;

class User
{
    public static function findByIdentifier(string $identifier): ?array
    {
        $identifier = trim($identifier);

        if ($identifier === '') {
            return null;
        }

        $user = Database::getInstance()->table('users')
            ->where('username', $identifier)
            ->first();

        if (!$user) {
            $user = Database::getInstance()->table('users')
                ->where('email', $identifier)
                ->first();
        }

        if (!$user) {
            return null;
        }

        return [
            'id' => (int) ($user['id'] ?? 0),
            'username' => (string) ($user['username'] ?? ''),
            'email' => (string) ($user['email'] ?? ''),
            'nama_lengkap' => (string) ($user['nama_lengkap'] ?? ''),
            'role' => (string) ($user['role'] ?? 'author'),
            'password' => (string) ($user['password'] ?? ''),
            'is_active' => (int) ($user['is_active'] ?? 0),
        ];
    }
}
