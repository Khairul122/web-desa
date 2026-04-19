<?php

namespace App\Models;

use App\Core\Database;

class Berita
{
    public static function latest($limit = 3)
    {
        return Database::getInstance()->table('berita')
            ->where('status', 'publish')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }

    public static function all($limit = 10)
    {
        return Database::getInstance()->table('berita')
            ->where('status', 'publish')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get();
    }
}
