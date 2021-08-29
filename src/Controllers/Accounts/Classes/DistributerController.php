<?php

namespace App\Controllers\Accounts\Classes;


use App\Models\Tools\Classes\ConnectDB;
use App\Models\Tools\Classes\LoginVerification;
use App\Models\Accounts\DistributerManager;
use App\Domain\Accounts\Classes\Distributer;
use App\Views\Accounts\Classes\DistributerViews;
use App\Controllers\Accounts\Interfaces\AccountControllerInterface;

class DistributerController implements AccountControllerInterface
{
    use \App\Controllers\Tools\Connect;

    public static function  index(): void
    {
        if(DistributerController::is_connected('distributer')===false){
            self::redirectory('login');
        }
        DistributerViews::index();
    }

    public static function login(): void
    {
        if(DistributerController::is_connected('distributer')!==false){
            DistributerController::redirectory('index');
        }

        if(!empty($_POST)){
            $distributer = new Distributer($_POST);
            $loginVerification = new LoginVerification(ConnectDB::getInstanceToPDO());
            $distributer = $loginVerification->account_verify($distributer);

            if(!is_array($distributer)){
                $_SESSION['distributer'] =["id"=>$distributer->id(),"nameDistrib"=>$distributer->nameDistrib(),"email"=>$distributer->email()];
                DistributerController::redirectory('index');
            }

            DistributerViews::login($distributer);
        }else{

            DistributerViews::login();
        }
    }

    public static function signup(): void
    {
        if(self::is_connected('distributer')===false){

            if(!empty($_POST)){
                if($_POST['password']!==$_POST['confirmation_password']){
                    DistributerViews::signup();
                    return;  
                }
                $distributer = new Distributer($_POST);
                $manager = new DistributerManager(ConnectDB::getInstanceToPDO());
                $manager ->add($distributer);
                self::redirectory('login');
            }
            DistributerViews::signup();
        }else{
            self::redirectory('login');
        }
    }

    public static function account(): void
    {
        if(DistributerController::is_connected('distributer')===false){
            self::redirectory('login');
        }
        $manager = new DistributerManager(ConnectDB::getInstanceToPDO());
        $distributer = $manager->getOnce($_SESSION['distributer']['id']);
        DistributerViews::account(["distributer" => $distributer]);
    }

    public static function logout(): void
    {
        session_destroy();
        header('Location: /login');
    }

    public static function redirectory($page){
        header('Location: /'.$page);
        exit();
    }


}
