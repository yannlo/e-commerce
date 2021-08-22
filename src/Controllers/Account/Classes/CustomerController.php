<?php

namespace App\Controllers\Account\Classes;


use App\Models\Tools\ConnectDB;
use App\Domain\Classes\Accounts\Customer;
use  App\Views\Account\Classes\CustomerViews;
use App\Models\Classes\Account\CustomerManager;
use App\Controllers\Account\Interfaces\AccountInterface;

class CustomerController implements AccountInterface
{
    use \App\Controllers\Tools\Connect;
    public static function  index(): void
    {
        $data=["customer"=>self::is_connected('customer')];
        CustomerViews::index($data);
    }

    public static function login(): void
    {
        if(self::is_connected('customer')!==false){
            self::homeRedirectory();
        }

        if(!empty($_POST)){
            $customer = new Customer($_POST);
            $manager = new CustomerManager(ConnectDB::getInstanceToPDO());
            $customer = $manager->customer_verify($customer);

            if(!is_array($customer)){
                $_SESSION['customer'] =["firstName"=>$customer->firstName(), "lastName"=>$customer->lastName(),"id"=>$customer-> id(),'email'=>$customer->email()];
                self::homeRedirectory();
            }

            CustomerViews::login($customer);
        }else{
            CustomerViews::login();
        }
    }

    public static function signup(): void
    {
        if(is_bool(self::is_connected('customer'))){
            if(!empty($_POST)){
                $customer = new Customer($_POST);
                if($_POST['password']!==$_POST['confirmation_password']){
                    CustomerViews::signup();
                    return;
                }
                if(isset($customer->email)OR isset($customer->password)){
                    CustomerViews::signup();
                    return;
                }
                $manager = new CustomerManager(ConnectDB::getInstanceToPDO());
                $manager ->add($customer);
                self::homeRedirectory();
            }

            CustomerViews::signup();
        }else{
            self::homeRedirectory();
        }
    }

    public static function account(): void
    {
        if(CustomerController::is_connected('customer')!== false){
            $data=["customer"=>CustomerController::is_connected('customer')];
            CustomerViews::account($data);
            return;
        }

        CustomerController::homeRedirectory();
    }

    public static function logout(): void
    {
        session_destroy();
        CustomerController::homeRedirectory();

    }

    private static function homeRedirectory(){
        header('Location: / ');
        exit();
    }


}