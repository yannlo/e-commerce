<?php

namespace App\Controllers\Orders;

use App\Domain\Orders\Order;
use App\Domain\Orders\OrderLine;
use App\Views\Orders\OrderViews;
use App\Models\Items\ItemManager;
use App\Controllers\Tools\Connect;
use App\Models\Tools\Classes\ConnectDB;
use App\Domain\Accounts\Classes\Customer;
use App\Models\Orders\Classes\OrderManager;
use App\Models\Orders\Classes\SaveOrders\MySQLSaveOrder;
use App\Models\Orders\Classes\SaveOrders\CookiesSaveOrder;
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
        // initialization to cart
        if(Connect::is_connected('customer'))
        {
            $customer = new Customer($_SESSION['customer']);
            if(isset($_COOKIE["cart"]))
            {
                self::$manager= new OrderManager(new CookiesSaveOrder());
                $cart = self::$manager->orderSaver()->getCart();
                self::$manager->orderSaver()->delete(null);
            }
            
            self::$manager= new OrderManager(new MySQLSaveOrder(ConnectDB::getInstanceToPDO()));
            
            $cartByMySQL = self::$manager->orderSaver()->getCartByCustomerIfExist($customer);
            if($cartByMySQL === false && !isset($cart))
            {
                $cart = new Order(['customer' => $customer]);
                self::$manager->orderSaver()->add($cart);
                $cart = self::$manager->orderSaver()->getCartByCustomerIfExist($customer);
            }
            else if($cartByMySQL !== false && !isset($cart))
            {
                $cart = $cartByMySQL;
            }
            else if($cartByMySQL !== false && isset($cart))
            {
                foreach($cart->orderLines() as $orderLine)
                {
                    $orderLine ->setOrder($cartByMySQL);
                    $cartByMySQL->addOrderLine($orderLine);
                }
                $cart = $cartByMySQL;
                self::$manager->orderSaver()->update($cart);

            }
    
        }
        else
        {
            self::$manager= new OrderManager(new CookiesSaveOrder());
            
            if(isset($_COOKIE["cart"]))
            {
                $cart = self::$manager->orderSaver()->getCart();
            }
            else
            {
                $cart = new Order([]);
                self::$manager->orderSaver()->add($cart);
                
            }
        }

        // update cart
        if(!empty($_POST))
        {
            ksort($_POST);
            $data = $_POST;
            $itemManager = new ItemManager(ConnectDB::getInstanceToPDO());
            $data['item'] = $itemManager->getOnce($data['item']);
            $data['order'] = $cart;
            $orderLine = new OrderLine($data);
            $cart->addOrderLine($orderLine);
            self::$manager->orderSaver()->update($cart);
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