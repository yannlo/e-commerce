<?php

namespace App\Controllers\Tools;

use App\Domain\Classes\Item;

class URLFormat
{
    public static function itemFormat(Item $item): string
    {
        $format = URLFormat::slugItemFormat($item)."-".$item->id();
        return $format;
    }
    public static function slugItemFormat(Item $item): string
    {
        $format = preg_replace('[\ ]','-',$item->itemName());
        return $format;
    }
}