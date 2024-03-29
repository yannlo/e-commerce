<?php

namespace App\Domain\Tools;

class NumberFormat
{
    public static function priceFormat(int $price,string $currentype= "Fcfa"): string
    {
        return number_format(num: (float) $price, decimal_separator:'',thousands_separator:' '). " ".$currentype;
    }
}