<?php 
session_start();

require_once '../../vendor/autoload.php';
require_once '../../config/config.php';
require_once '../../config/config_db.php';




use App\Controllers\Account\Classes\{
    CustomerController,
    DistributerController
};
use App\Controllers\ItemController;

use App\Views\Generals\ErrorViews;




$router = new AltoRouter();

$router -> addMatchTypes(['slh'=>'[/]?$']);

// home
$router->map('GET','/', function(){
    http_response_code(200);
    CustomerController::index();
    return;

});

$router->map('GET','/[a:page][slh]',function($page){
    if(!method_exists(new CustomerController,$page)){
        http_response_code(404);
        ErrorViews::error_404($page);
        
    }else{
        http_response_code(200);
        CustomerController::$page();
    }
});

$router->map('POST','/[a:page][slh]',function($page){

    if(!in_array($page,['signup','login'])){
        http_response_code(404);
        ErrorViews::error_404($page);

    }else{
        http_response_code(200);
        CustomerController::$page();
    }

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

    }else{
        http_response_code(200);
        ItemController::$page();
    }

});

$router->map('POST','/item/[a:page][slh]',function($page){

    if(!in_array($page,['add','delete'])){
        http_response_code(404);
        ErrorViews::error_404($page);

    }else{
        http_response_code(200);
        ItemController::$page();
    }

});





$match = $router->match();

if(is_array($match)){
    call_user_func_array($match['target'],$match['params']);
}else{
    http_response_code(404);
    ErrorViews::error_404($page);
}



exit();



