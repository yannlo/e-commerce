<?php

namespace  App\Controllers\Accounts\Classes;


use App\Controllers\Tools\Connect;
use  App\Models\Tools\Classes\ConnectDB;
use App\Models\Accounts\CustomerManager;
use  App\Domain\Accounts\Classes\Customer;
use  App\Views\Accounts\Classes\CustomerViews;
use  App\Models\Tools\Classes\LoginVerification;
use App\Models\Tools\Classes\Exceptions\LoginVerificationException;
use  App\Controllers\Accounts\Interfaces\AccountControllerInterface;

class CustomerController implements AccountControllerInterface
{
    public static function  index(): void
    {
        CustomerViews::index();
    }

    public static function login(): void
    {
        if(Connect::TypeConnectionVerify('customer')){
            self::homeRedirectory();
        }

        if(empty($_POST))
        {
            CustomerViews::login();
            return;

        }


        $customer = new Customer($_POST);
        $loginVerification = new LoginVerification(ConnectDB::getInstanceToPDO());
        try
        {
            $customer = $loginVerification->account_verify($customer);

        }
        catch(LoginVerificationException $e)
        {
            $data['error'] = $e->getMessage();
            CustomerViews::login($data);
            return;
        }

        $data =[
            "firstName"=>$customer->firstName(),
            "lastName"=>$customer->lastName(),
            "id"=>$customer-> id(),
            'email'=>$customer->email()
        ];

        Connect::userConnection('customer', $data);
        self::homeRedirectory();
    }

    public static function signup(): void
    {
        if(Connect::typeConnectionVerify('customer')){
            self::homeRedirectory();
            return;
        }

        if(!isset($_POST)){
            CustomerViews::signup();
            return;
        }
        
        if(!isset($_POST['email']) OR  !isset($_POST['password'])){
            CustomerViews::signup();
            return;
        }

        
        if($_POST['password']!==$_POST['confirmation_password']){
            CustomerViews::signup();
            return;
        }
        
        $customer = new Customer($_POST);
        $manager = new CustomerManager(ConnectDB::getInstanceToPDO());

        $manager ->add($customer);
        
        self::homeRedirectory();
    }

    public static function account(): void
    {
        if(!Connect::typeConnectionVerify('customer')){
            self::homeRedirectory();
        }
        
        $data=[
            "customer"=>Connect::getUser('customer')
        ];
        CustomerViews::account($data);
        return;
        
    }

    public static function logout(): void
    {
        session_destroy();
        self::homeRedirectory();

    }

    private static function homeRedirectory(){
        header('Location: / ');
        exit();
    }


}