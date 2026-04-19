<?php

namespace App\Models;

use App\Core\Database;

class Pengaturan
{
    public static function get($nama_setting, $default = '')
    {
        $result = Database::getInstance()->table('pengaturan')
            ->where('nama_setting', $nama_setting)
            ->first();
        return $result['nilai_setting'] ?? $default;
    }

    public static function getAll()
    {
        $settings = Database::getInstance()->table('pengaturan')->get();
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['nama_setting']] = $setting['nilai_setting'];
        }
        return $result;
    }
}
