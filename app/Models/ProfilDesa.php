<?php

namespace App\Models;

use App\Core\Database;

class ProfilDesa
{
    public static function first()
    {
        return Database::getInstance()->table('profil_desa')->first();
    }

    public static function getNamaDesa()
    {
        $profil = self::first();
        return $profil['nama_desa'] ?? 'Desa';
    }

    public static function getAlamat()
    {
        $profil = self::first();
        return $profil['alamat'] ?? '';
    }

    public static function getTelepon()
    {
        $profil = self::first();
        return $profil['telepon'] ?? '';
    }

    public static function getEmail()
    {
        $profil = self::first();
        return $profil['email'] ?? '';
    }

    public static function getSejarah()
    {
        $profil = self::first();
        return $profil['sejarah'] ?? '';
    }

    public static function getVisiMisi()
    {
        $profil = self::first();
        return $profil['visi_misi'] ?? '';
    }
}
