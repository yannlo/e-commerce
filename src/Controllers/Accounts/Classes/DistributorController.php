<?php

namespace App\Controllers\Accounts\Classes;


use App\Models\Items\ItemManager;
use App\Controllers\Tools\Connect;
use App\Models\Tools\Classes\ConnectDB;
use App\Models\Accounts\DistributorManager;
use App\Domain\Accounts\Classes\Distributor;
use App\Models\Tools\Classes\LoginVerification;
use App\Views\Accounts\Classes\DistributorViews;
use App\Controllers\Accounts\Interfaces\AccountControllerInterface;
use App\Models\Tools\Classes\Exceptions\LoginVerificationException;

class DistributorController implements AccountControllerInterface
{
    public static function  index(): void
    {
        if(!Connect::typeConnectionVerify('distributor')){
            self::redirectory('login');
        }
        DistributorViews::index();
    }

    public static function login(): void
    {
        if(Connect::typeConnectionVerify('distributor')){
            self::redirectory('index');
        }

        if(empty($_POST)){
            DistributorViews::login();
            return;
        }

        $distributor = new Distributor($_POST);
        
        $loginVerification = new LoginVerification(ConnectDB::getInstanceToPDO());

        try
        {
            $distributor = $loginVerification->account_verify($distributor);
        }
        catch(LoginVerificationException $e)
        {
            $data['error'] = $e->getMessage();
            DistributorViews::login($data);
            return;
        }

        $data =["id"=>$distributor->id(),"nameDistrib"=>$distributor->nameDistrib(),"email"=>$distributor->email()];
        Connect::userConnection('distributor', $data);
        self::redirectory('index');
        
    }

    public static function signup(): void
    {
        if(Connect::typeConnectionVerify('distributor')){
            self::redirectory('');
        }

        if(empty($_POST)){
            DistributorViews::signup();
            return;
        }

        if($_POST['password']!==$_POST['confirmation_password']){
            DistributorViews::signup();
            return;  
        }

        $distributor = new Distributor($_POST);
        $manager = new DistributorManager(ConnectDB::getInstanceToPDO());
        $manager ->add($distributor);
        self::redirectory('login');
    }

    public static function account(): void
    {
        if(!Connect::typeConnectionVerify('distributor')){
            self::redirectory('login');
        }

        $manager = new DistributorManager(ConnectDB::getInstanceToPDO());
        $data = Connect::getUser();
        $distributor = $manager->getOnce($data['id']);
        
        DistributorViews::account(["distributor" => $distributor]);
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

    public static function items(): void
    {
        if(!Connect::typeConnectionVerify('distributor')){
            self::redirectory('login');
        }
        $distributor = new Distributor(Connect::getUser());
        
        $itemManager = new ItemManager(ConnectDB::getInstanceToPDO());
        
        $data['items'] = $itemManager -> getByDistributor($distributor);
        
        DistributorViews::items($data);
    }


}
