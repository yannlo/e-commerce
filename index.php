<?php 

require_once 'vendor/autoload.php';
require_once 'config/config.php';

use App\Controllers\Account\Classes\{
    CustomerController,
    DistributerController
};
use App\Controllers\ItemController;

use App\Domain\Classes\Item;
use App\Views\Generals\ErrorViews;

if(isset($_GET['section'])){
    switch($_GET['section']){

        case 'item':
            if(!isset($_GET['page']))
            {
                CustomerController::index();
                break;
            }
            
            if($_GET['page']!=="item")
            {
                ErrorViews::error_404();
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
                ErrorViews::error_404();
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
                ErrorViews::error_404();
            }

            break;

        default:

            ErrorViews::error_404();
            break;

    }
}
else
{
    ErrorViews::error_404();
}

exit();



