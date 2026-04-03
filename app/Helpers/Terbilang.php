<?php

namespace App\Helpers;

class Terbilang
{
    public static function convert($x)
    {
        $abil = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        if ($x < 12)
            return " " . $abil[$x];
        elseif ($x < 20)
            return self::convert($x - 10) . " belas";
        elseif ($x < 100)
            return self::convert($x / 10) . " puluh" . self::convert($x % 10);
        elseif ($x < 200)
            return " seratus" . self::convert($x - 100);
        elseif ($x < 1000)
            return self::convert($x / 100) . " ratus" . self::convert($x % 100);
        elseif ($x < 2000)
            return " seribu" . self::convert($x - 1000);
        elseif ($x < 1000000)
            return self::convert($x / 1000) . " ribu" . self::convert($x % 1000);
        elseif ($x < 1000000000)
            return self::convert($x / 1000000) . " juta" . self::convert($x % 1000000);

        return "Angka terlalu besar";
    }
}
