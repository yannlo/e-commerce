<?php

namespace App\Controllers\Accounts\Classes;


use App\Controllers\Tools\Connect;
use App\Models\Tools\Classes\ConnectDB;
use App\Models\Accounts\DistributerManager;
use App\Domain\Accounts\Classes\Distributer;
use App\Models\Tools\Classes\LoginVerification;
use App\Views\Accounts\Classes\DistributerViews;
use App\Controllers\Accounts\Interfaces\AccountControllerInterface;
use App\Models\Tools\Classes\Exceptions\LoginVerificationException;

class DistributerController implements AccountControllerInterface
{
    public static function  index(): void
    {
        if(!Connect::typeConnectionVerify('distributer')){
            self::redirectory('login');
        }
        DistributerViews::index();
    }

    public static function login(): void
    {
        if(!Connect::typeConnectionVerify('distributer')){
            self::redirectory('index');
        }

        if(empty($_POST)){
            DistributerViews::login();
            return;
        }

        $distributer = new Distributer($_POST);
        
        $loginVerification = new LoginVerification(ConnectDB::getInstanceToPDO());

        try
        {
            $distributer = $loginVerification->account_verify($distributer);
        }
        catch(LoginVerificationException $e)
        {
            $data['error'] = $e->getMessage();
            DistributerViews::login($data);
            return;
        }

        $data =["id"=>$distributer->id(),"nameDistrib"=>$distributer->nameDistrib(),"email"=>$distributer->email()];
        Connect::userConnection('distributer', $data);
        self::redirectory('index');
        
    }

    public static function signup(): void
    {
        if(Connect::typeConnectionVerify('distributer')){
            self::redirectory('');
        }

        if(empty($_POST)){
            DistributerViews::signup();
            return;
        }

        if($_POST['password']!==$_POST['confirmation_password']){
            DistributerViews::signup();
            return;  
        }

        $distributer = new Distributer($_POST);
        $manager = new DistributerManager(ConnectDB::getInstanceToPDO());
        $manager ->add($distributer);
        self::redirectory('login');
    }

    public static function account(): void
    {
        if(!Connect::typeConnectionVerify('distributer')){
            self::redirectory('login');
        }

        $manager = new DistributerManager(ConnectDB::getInstanceToPDO());
        $data = Connect::getUser();
        $distributer = $manager->getOnce($data['id']);
        
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
