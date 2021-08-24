<?php

namespace  App\Controllers\Account\Classes;


use  App\Models\Tools\ConnectDB;
use  App\Models\Tools\LoginVerification;
use App\Models\Accounts\CustomerManager;
use  App\Domain\Accounts\Classes\Customer;
use  App\Views\Accounts\Classes\CustomerViews;
use  App\Controllers\Account\Interfaces\AccountControllerInterface;

class CustomerController implements AccountControllerInterface
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
            $loginVerification = new LoginVerification(ConnectDB::getInstanceToPDO());
            $customer = $loginVerification->account_verify($customer);

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