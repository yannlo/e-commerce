<?php

namespace App\Controllers;

use App\Views\itemViews;
use App\Models\ItemModel;
use App\Domain\Classes\Item;

class ItemController 
{
    public static function item(Item $item): void
    {
        $data["item"] = $item;
        ItemViews::item($data);
    }
}

