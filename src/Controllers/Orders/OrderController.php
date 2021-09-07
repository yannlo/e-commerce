<?php

namespace App\Controllers\Orders;

use App\Views\Orders\OrderViews;
use App\Controllers\Tools\Connect;
use App\Domain\Tools\JSONFormatter;
use App\Models\Tools\Classes\ConnectDB;
use App\Domain\Accounts\Classes\Customer;
use App\Models\Orders\Classes\OrderManager;
use App\Models\Orders\Classes\SaveOrders\MySQLSaveOrder;
use App\Models\Orders\Classes\Exceptions\MySQLSaveOrderException;
use App\Models\Orders\Classes\Exceptions\CookiesSaveOrderException;

class OrderController
{
    private static OrderManager $manager;

    public static function defineManager()
    {
        self::$manager = new OrderManager(null);
    }

    public static function cart():void
    {

        if(Connect::typeConnectionVerify('distributer'))
        {
            header('Location: /');
            exit();
        }

        $customer=null;

        if(Connect::typeConnectionVerify('customer'))
        {
            $customer = new Customer(Connect::getUser());
        }

        // initialization to cart
        $cart = CartAction::initCart();

        // update cart

        if(!empty($_GET))
        {
            $cart = CartAction::modifierCart($cart,$customer,$_GET,'delete');
            header('Location: /cart');
            exit();
        }
        
        if(!empty($_POST))
        {   
            if($_POST['submit']==='update'){
                $operation ="update";
            }
            else{
                $operation ="add";
            }
            CartAction::modifierCart($cart,$customer,$_POST,$operation);
        }
        
        OrderViews::Cart($cart);

    }


    public static function confirm():void
    {
        if(Connect::typeConnectionVerify('distributer'))
        {
            header('Location: /');
            exit();
        }

        $customer=null;

        if(Connect::typeConnectionVerify('customer'))
        {
            $customer = new Customer(Connect::getUser());
        }

        $cart = CartAction::initCart();

        $data['cart'] = $cart;

    }


    public static function history():void
    {
        if(!Connect::typeConnectionVerify('customer'))
        {
            header('Location: /');
            exit();
        }
        
        $customer = new Customer($_SESSION['customer']);
        self::defineManager();

        $manager = self::$manager;

        $manager->setOrderSaver(new MySQLSaveOrder(ConnectDB::getInstanceToPDO()));

        $orders=[];
        try
        {
            $orders = $manager-> orderSaver() ->getOnlyOrderByCustomer($customer);
        }
        catch (MySQLSaveOrderException $e)
        {
            $_SESSION["error"][]= $e -> getMessage();
        }
        catch (CookiesSaveOrderException $e)
        {
            $_SESSION["error"][]= $e->getMessage();
        }
    
        OrderViews::History($orders);
    }


}