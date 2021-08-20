<?php 

require_once '../vendor/autoload.php';
require_once '../config/config.php';



use App\Controllers\Account\Classes\{
    CustomerController,
    DistributerController
};
use App\Controllers\ItemController;

use App\Domain\Classes\Item;
use App\Views\Generals\ErrorViews;




$router = new AltoRouter();

$router -> addMatchTypes(['slh'=>'[/]?$']);

// home
$router->map('GET','/', function(){
    CustomerController::index();
});


// router items
$router->map('GET','/item[slh]', function(){

    CustomerController::index();

});

$router->map('GET','/item/[*:slug]-[i:id]?[slh]', function($slug,$id){

    $name = preg_replace('/-/',' ', $slug);	
    $item = new Item(["id"=>(int)$id, "itemName"=>"$name","price"=>5000]);
    ItemController::Item($item);
});


// router distributer
$router->map('GET','/distributer[slh]', function(){

    DistributerController::login();
    
});

$router->map('GET','/distributer/[a:page][slh]',function($page){

    if(!method_exists(new DistributerController,$page)){
        ErrorViews::error_404();

    }else{

        DistributerController::$page();
    }

});


// router customer
$router->map('GET','/customer[slh]', function(){

    CustomerController::index();

});

$router->map('GET','/customer/[a:page][slh]',function($page){

    if(!method_exists(new CustomerController,$page)){
        ErrorViews::error_404();
        
    }else{

        CustomerController::$page();
    }


});


$match = $router->match();

if(is_array($match)){
    call_user_func_array($match['target'],$match['params']);
}else{
    ErrorViews::error_404();
}



exit();



