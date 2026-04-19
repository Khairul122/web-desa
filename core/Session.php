<?php

namespace App\Core;

class Session
{
    private static $started = false;

    public static function start(): void
    {
        if (self::$started === false) {
            session_start();
            self::$started = true;
        }
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function get(string $key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function put(string $key, $value): void
    {
        self::set($key, $value);
    }

    public static function forget(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function pull(string $key, $default = null)
    {
        $value = self::get($key, $default);
        self::forget($key);
        return $value;
    }

    public static function flash(string $key, $value = null)
    {
        self::start();
        
        if ($value === null) {
            $value = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $value;
        }
        
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    public static function all(): array
    {
        self::start();
        return $_SESSION;
    }

    public static function flush(): void
    {
        self::start();
        $_SESSION = [];
    }

    public static function regenerate(bool $deleteOldSession = true): void
    {
        self::start();
        session_regenerate_id($deleteOldSession);
    }

    public static function invalidate(): void
    {
        self::flush();
        self::regenerate();
    }

    public static function token(): string
    {
        self::start();
        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_token'];
    }

    public static function csrfToken(): string
    {
        return self::token();
    }

    public static function validateCsrf(string $token): bool
    {
        self::start();
        return isset($_SESSION['_token']) && hash_equals($_SESSION['_token'], $token);
    }

    public static function setId(string $id): void
    {
        session_id($id);
    }

    public static function getId(): string
    {
        return session_id();
    }

    public static function savePath(string $path): void
    {
        session_save_path($path);
    }

    public static function status(): bool
    {
        return self::$started;
    }
}
