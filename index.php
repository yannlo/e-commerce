<?php 

require_once 'vendor/autoload.php';
require_once 'config/config.php';

use App\Controllers\Account\Classes\CustomerController;
use App\Controllers\Account\Classes\DistributerController;
use App\Controllers\ItemController;

use App\Domain\Classes\Item;

if(isset($_GET['action'])){
    switch($_GET['action']){

        case 'item':
            if(!isset($_GET['page']))
            {
                CustomerController::index();
                break;
            }
            
            if($_GET['page']!=="item")
            {
                CustomerController::index();
                break;
            }

            $item = new Item(["id"=>1, "itemName"=>"t-shirt","price"=>5000]);
            ItemController::Item($item);
            
            break;


        case 'distributer':
            if(!isset($_GET['page']))
            {
                DistributerController::login();
                break;
            }

            $method = $_GET['page'];

            try
            {
                DistributerController::$method();
            }

            catch(Exception $e)
            {
                echo $e->getMessage();
                DistributerController::login();
            }

            break;


        case 'customer':
            if(!isset($_GET['page']))
            {
                CustomerController::login();
                break;
            }

            $method = $_GET['page'];

            try
            {
                CustomerController::$method();
            }

            catch(Exception $e)
            {
                echo $e->getMessage();
                CustomerController::index();
            }

            break;

        default:

            CustomerController::index();
            break;

    }
}
else
{
    CustomerController::index();
}

exit();



