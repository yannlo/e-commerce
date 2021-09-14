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


    /**
     * index
     * 
     * print all distributor list
     *
     * @return void
     */
    public static function  index(): void
    {


    }

    
    /**
     * dashboard
     * 
     * permit to seen global productivity 
     *
     * @return void
     */
    public static function dashboard(): void
    {

    }
    
    /**
     * search
     *
     * permit to found distributor
     * 
     * @return void
     */
    public static function search(): void
    {

    }

    
    /**
     * preview
     *
     * @param  null|string $section
     * @return void
     */
    public static function preview(?string $section=null): void
    {
        
    }

    
    /**
     * distributor
     * 
     * get specific distributor
     *
     * @param  int $id
     * @param  string $slugs distributor name in url format
     * @param  null|string $section
     * @return void
     */
    public static function distributor(int $id,string $slugs, ?string $section=null): void
    {
        
    }
    
    /**
     * account
     * 
     * permit CRUD specific account information
     *
     * @param  null|string $section
     * @return void
     */
    public static function account(?string $section = null): void
    {
        if(!Connect::typeConnectionVerify('distributor')){
            self::redirectory('login');
        }

        $distributor  = Connect::getUser();
        
        DistributorViews::account(["distributor" => $distributor]);
    }
    
    /**
     * login
     *
     * permit to connection to distributor
     * 
     * @return void
     */
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

        Connect::userConnection('distributor', $distributor->id());
        self::redirectory('index');
        
    }
    
    /**
     * signup
     *
     * permit distributor inscription
     * 
     * @return void
     */
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
    
    /**
     * logout
     * 
     * permit to disconnected distributor
     *
     * @return void
     */
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
