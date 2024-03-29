<?php 
session_name("Abj-Style\distributor");
session_start();

require_once '../../vendor/autoload.php';
require_once '../../config/config.php';
require_once '../../config/config_db.php';




use  App\Controllers\Accounts\Classes\DistributorController;
use  App\Controllers\Items\ItemController;
use  App\Views\Generals\Classes\ErrorViews;




$router = new AltoRouter();

$router -> addMatchTypes(['slh'=>'[/]?$']);

// home
$router->map('GET','/', function(){
    http_response_code(200);
    DistributorController::index(); 
    return;
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

// router distributor

$router->map('GET','/[a:page][slh]',function($page){

    if(!method_exists(new DistributorController,$page)){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }
    
    http_response_code(200);
    DistributorController::$page();
    

});

$router->map('POST','/[a:page][slh]',function($page){

    if(!in_array($page,['signup','login'])){
        http_response_code(404);
        ErrorViews::error_404($page);
        return;
    }

    http_response_code(200);
    DistributorController::$page();

});

$match = $router->match();

if(is_array($match)){
    call_user_func_array($match['target'],$match['params']);
}else{
    http_response_code(404);
    ErrorViews::error_404($page);
}



exit();



