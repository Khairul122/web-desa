<?php

namespace App\Models;

use App\Core\Database;

class Carousel
{
    public static function active()
    {
        return Database::getInstance()->table('carousel')
            ->where('is_active', 1)
            ->orderBy('urutan', 'ASC')
            ->get();
    }
}
