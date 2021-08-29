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
use App\Models\Orders\Classes\OrderLineManager;
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
        if(Connect::is_connected('customer'))
        {
            $customer = new Customer($_SESSION['customer']);
        }else
        {
            $customer=null;
        }

        // initialization to cart
        $cart = OrderController::initCart();

        // update cart

        if(!empty($_GET))
        {
            $cart = OrderController::modifierCart($cart,$customer,$_GET,'delete');
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
            OrderController::modifierCart($cart,$customer,$_POST,$operation);
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


    private static function initCart(): Order
    {
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
                    if($cartByMySQL->foundOrderLine($orderLine)===false)
                    {
                        $orderLine ->setOrder($cartByMySQL);
                        $cartByMySQL->addOrderLine($orderLine);
                    }
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


        return $cart;
    }


    private static function modifierCart(Order $cart,?Customer $customer,array $dataRequest, string $operation)
    {
        ksort($dataRequest);
        $data = $dataRequest;

        if($operation==='delete')
        {
            OrderController::deleteCart($data,$cart);
        }
        else if($operation==='update')
        {   
            OrderController::updateCart($data,$cart);
        }
        else
        {
            OrderController::addCart($data,$cart);
        }


        self::$manager->orderSaver()->update($cart);

        if(Connect::is_connected('customer'))
        {
            self::$manager= new OrderManager(new MySQLSaveOrder(ConnectDB::getInstanceToPDO()));    
            $cart = self::$manager->orderSaver()->getCartByCustomerIfExist($customer);
        }
        else
        {
            self::$manager= new OrderManager(new CookiesSaveOrder());
            $cart = self::$manager->orderSaver()->getCart();
        }
        

    }

    public static function deleteCart(array $data,Order $cart)
    {

        $data['id'] = $data['delete'];
        unset($data['delete']);
        $itemManager = new ItemManager(ConnectDB::getInstanceToPDO());
        $data['item'] = $itemManager->getOnce($data['item']);
        $data['order'] = $cart;
        $orderLine = new OrderLine($data);
        $OrderLineExist = false;

        foreach ($cart->orderLines() as $orderLineInCart)
        {
            if($orderLineInCart->item()->id() === $orderLine->item()->id())
            {
                $OrderLineExist = true;
            }
        }

        if ($OrderLineExist) {
            if(Connect::is_connected('customer'))
            {
                $orderLineManager = new OrderLineManager(ConnectDB::getInstanceToPDO());
                $orderLineManager->delete($orderLine);
            }
            $cart->unsetOrderLine($orderLine,true);
        }
        return $cart;

    }

    public static function updateCart(array $data,Order $cart)
    {
        $itemManager = new ItemManager(ConnectDB::getInstanceToPDO());
        $data['item'] = $itemManager->getOnce($data['item']);
        $data['order'] = $cart;
        $orderLine = new OrderLine($data);
        
        $OrderLineExist = false;
        foreach ($cart->orderLines() as $orderLineInCart)
        {
            if($orderLineInCart->item()->id() == $orderLine->item()->id())
            {
                $OrderLineExist = true;
            }
        }

        if ($OrderLineExist) {
            $cart->setOrderLine($orderLine);
        }


    }

    public static function addCart(array $data,Order $cart)
    {
        $itemManager = new ItemManager(ConnectDB::getInstanceToPDO());
        $data['item'] = $itemManager->getOnce($data['item']);
        $data['order'] = $cart;
        
        $orderLine = new OrderLine($data);

        $OrderLineExist = false;
        foreach ($cart->orderLines() as $orderLineInCart)
        {
            if($orderLineInCart->item() == $orderLine->item())
            {
                $OrderLineExist = true;
            }
        }

        if (!$OrderLineExist) {
            $cart->addOrderLine($orderLine);
        }
    }

}