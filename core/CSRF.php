<?php

namespace App\Core;

class CSRF
{
    private static $tokenField = '_token';
    private static $tokenName = '_csrf_token';

    public static function generateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set(self::$tokenName, $token);
        return $token;
    }

    public static function getToken(): string
    {
        if (!Session::has(self::$tokenName)) {
            return self::generateToken();
        }
        return Session::get(self::$tokenName);
    }

    public static function tokenField(): string
    {
        return '<input type="hidden" name="' . self::$tokenField . '" value="' . self::getToken() . '">';
    }

    public static function tokenInput(): string
    {
        return self::tokenField();
    }

    public static function validate(array $data = []): bool
    {
        $token = $data[self::$tokenField] ?? null;
        
        if (!$token || !Session::has(self::$tokenName)) {
            return false;
        }
        
        return hash_equals(Session::pull(self::$tokenName), $token);
    }

    public static function validateRequest(): bool
    {
        $request = Request::capture();
        $token = $request->post(self::$tokenField) ?? $request->getHeader(self::$tokenField);
        return self::validate([self::$tokenField => $token]);
    }

    public static function refresh(): string
    {
        return self::generateToken();
    }

    public static function setFieldName(string $name): void
    {
        self::$tokenField = $name;
    }

    public static function setTokenName(string $name): void
    {
        self::$tokenName = $name;
    }
}
