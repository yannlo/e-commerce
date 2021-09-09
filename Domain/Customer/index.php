<?php 
session_start();

require_once '../../vendor/autoload.php';
require_once '../../config/config.php';
require_once '../../config/config_db.php';




use App\Controllers\Tests\TestController;
use  App\Controllers\Items\ItemController;
use  App\Views\Generals\Classes\ErrorViews;
use App\Controllers\Orders\OrderController;
use  App\Controllers\Accounts\Classes\CustomerController;


$router = new AltoRouter();

$router -> addMatchTypes(['slh'=>'[/]?$']);

// home
$router->map('GET','/', function(){
    http_response_code(200);
    CustomerController::index();
    return;

});

// test
$router->map('GET','/test/[a:page][slh]', function($page){

    if(!method_exists(new TestController,$page)){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }

    http_response_code(200);
    TestController::$page();

    return;

});


// cart
$router->map('POST','/cart[slh]',function(){
    http_response_code(200);
    OrderController::cart();
});

$router->map('GET','/cart[slh]',function(){
    http_response_code(200);
    OrderController::cart();
});


// router users pages
$router->map('GET','/[a:page][slh]',function($page){
    if(!method_exists(new CustomerController,$page)){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }

    http_response_code(200);
    CustomerController::$page();

});



$router->map('POST','/[a:page][slh]',function($page){

    if(!in_array($page,['signup','login'])){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }

    http_response_code(200);
    CustomerController::$page();

});


// router items
$router->map('GET','/item[slh]', function(){
    http_response_code(200);
    ItemController::list();

});


$router->map('GET','/item/[*:slug]-[i:id][slh]', function($slug,$id){
    ItemController::Item($id, $slug);
});


$router->map('GET','/item/[a:page][slh]',function($page){

    if(!method_exists(new ItemController,$page)){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }

    http_response_code(200);
    ItemController::$page();
});

$router->map('POST','/item/[a:page][slh]',function($page){

    if(!in_array($page,['add','delete'])){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }

    http_response_code(200);
    ItemController::$page();
});


// route orders
$router->map('GET','/order/[a:page][slh]',function($page){

    if(!method_exists(new OrderController,$page) || in_array($page,['address','delivery','payment'])){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }

    http_response_code(200);
    OrderController::$page();
});


$router->map('GET','/orderConfirmation/[a:page][slh]',function($page){

    if(!in_array($page,['address','delivery','payment'])){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }

    http_response_code(200);
    OrderController::$page();
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



