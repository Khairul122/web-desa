<?php

namespace App\Models;

use App\Core\Database;

class StatistikDesa
{
    public static function all()
    {
        return Database::getInstance()->table('statistik_desa')->orderBy('urutan', 'ASC')->get();
    }

    public static function get()
    {
        return Database::getInstance()->table('statistik_desa')->orderBy('urutan', 'ASC')->get() ?? [];
    }
}
