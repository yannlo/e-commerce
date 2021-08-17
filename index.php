<?php 

require_once 'vendor/autoload.php';

use App\classes\Item;

$item = new Item(array("itemName"=>"t-shirt","price"=>10000));

echo "<pre>";
var_dump($item);
echo "</pre>";