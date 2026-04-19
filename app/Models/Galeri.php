<?php

namespace App\Models;

use App\Core\Database;

class Galeri
{
    public static function latest($limit = 5)
    {
        return Database::getInstance()->table('galeri')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public static function all()
    {
        return Database::getInstance()->table('galeri')
            ->orderBy('created_at', 'DESC')
            ->get();
    }
}
