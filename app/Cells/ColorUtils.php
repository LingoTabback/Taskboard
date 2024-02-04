<?php

namespace App\Cells;

class ColorUtils
{
    private static array $colors = [
        '#B71C1C', '#880E4F', '#4A148C', '#311B92', '#1A237E', '#0D47A1', '#01579B',
        '#006064', '#004D40', '#1B5E20', '#33691E', '#33691E', '#FF6F00', '#BF360C'
    ];

    public static function colorFromId(int $id): string
    {
        $key = abs($id);
        $key = ~$key + ($key << 15);
        $key = $key ^ ($key >> 12);
        $key = $key + ($key << 2);
        $key = $key ^ ($key >> 4);
        $key = $key * 2057;
        $key = $key ^ ($key >> 16);

        return ColorUtils::$colors[$key % 14];
    }
}
