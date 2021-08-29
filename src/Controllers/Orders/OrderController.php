<?php

namespace App\Controllers\Orders;

use App\Domain\Orders\Order;
use App\Views\Orders\OrderViews;
use App\Controllers\Tools\Connect;
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

    public static function cart()
    {
        if(Connect::is_connected('customer'))
        {
            $customer = new Customer($_SESSION['customer']);
        }else
        {
            $customer=null;
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

    public static function history()
    {
        if(!Connect::is_connected('customer'))
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