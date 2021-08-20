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




$router = new AltoRouter();


// home
$router->map('GET','/', function(){
    echo "Page d'accueil";
});


// router items
$router->map('GET','/item', function(){

    CustomerController::index();

});

$router->map('GET','/item/[*:slug]-[i:id]?', function($slug,$id){

    $name = preg_replace('/-/',' ', $slug);	
    $item = new Item(["id"=>(int)$id, "itemName"=>"$name","price"=>5000]);
    ItemController::Item($item);
});


// router distributer
$router->map('GET','/distributer', function(){

    DistributerController::login();
    
});

$router->map('GET','/distributer/[a:page]',function($page){

    try
    {
        DistributerController::$page();
    }

    catch(Exception $e)
    {
        echo $e->getMessage();
        ErrorViews::error_404();
    }
});

// router customer
$router->map('GET','/customer', function(){

    CustomerController::index();

});

$router->map('GET','/customer/[a:page]',function($page){

    try
    {
        CustomerController::$page();
    }

    catch(Exception $e)
    {
        echo $e->getMessage();
        ErrorViews::error_404();
    }
});


// router error

// $router->map('GET','/[*]',function(){
//     ErrorViews::error_404();
// });

$match = $router->match();

if($match!==null){
    call_user_func_array($match['target'],$match['params']);
}



exit();



