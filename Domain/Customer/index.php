<?php 
session_name("Abj-Style\customer");
session_start();

require_once '../../vendor/autoload.php';
require_once '../../config/config.php';
require_once '../../config/config_db.php';


use  App\Controllers\Items\ItemController;
use App\Controllers\Orders\CartController;
use  App\Views\Generals\Classes\ErrorViews;
use App\Controllers\Orders\OrderController;
use App\Controllers\Generals\GeneralController;
use  App\Controllers\Accounts\Classes\CustomerController;
use App\Controllers\Accounts\Classes\DistributorController;


$router = new AltoRouter();

$router -> addMatchTypes(['slh'=>'[/]?$']);

// home
$router->map('GET','/', function(){
    http_response_code(200);
    GeneralController::index();
    return;

});

// account
$router->map('GET|POST','/account/[a:page][slh]',function($page){
    http_response_code(200);
    CartController::confirm($page);
});

// cart
$router->map('GET|POST','/cart[slh]',function(){
    http_response_code(200);
    CartController::index();
});

$router->map('GET|POST','/cart/confirm/[a:page][slh]',function($page){
    if(!in_array($page,['address','delivery','payment'])){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }
    http_response_code(200);
    CartController::confirm($page);
});


// router items
$router->map('GET','/items[slh]', function(){
    http_response_code(200);
    ItemController::index();

});

$router->map('GET','/items/[*:slug]-[i:id][slh]', function($slug, $id){
    ItemController::Item($id, $slug);
});


$router->map('GET|POST','/items/[*:slug]-[i:id]/[a:section][slh]', function($slug, $id, $section){
    ItemController::Item($id, $slug, $section);
});


$router->map('GET|POST','/items/[a:page][slh]',function($page){

    if(!method_exists(new ItemController,$page)){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }

    http_response_code(200);
    ItemController::$page();
});


// router distributor
$router->map('GET','/distributors[slh]', function(){
    http_response_code(200);
    DistributorController::index();

});

$router->map('GET','/distributors/[a:page][slh]',function($page){

    if(!method_exists(new DistributorController,$page)){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }

    http_response_code(200);
    DistributorController::$page();
});


$router->map('GET','/distributors/[*:slug]-[i:id][slh]', function($slug, $id){
    DistributorController::distributor($id, $slug);
});

$router->map('GET|POST','/distributors/[*:slug]-[i:id]/[a:section][slh]', function($slug, $id, $section){
    DistributorController::distributor($id, $slug, $section);
});




// route orders
$router->map('GET','/orders/[a:page][slh]',function($page){

    if(!method_exists(new OrderController,$page)){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }

    http_response_code(200);
    OrderController::$page();
});

$router->map('GET','/orders/[a:id][slh]',function($id){

    http_response_code(200);
    OrderController::$id();
});

$router->map('GET|POST','/orders/[i:id]/[a:page][slh]',function($id,$page){

    http_response_code(200);
    OrderController::$id($page);
});


// router users pages
$router->map('GET|POST','/[a:page][slh]',function($page){
    if(!method_exists(new CustomerController,$page)){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }

    http_response_code(200);
    CustomerController::$page();

});


$match = $router->match();

if(is_array($match)){
    call_user_func_array($match['target'],$match['params']);
}
else{
    http_response_code(404);
    ErrorViews::error_404($page);
}

exit();



