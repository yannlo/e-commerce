<?php

namespace App\Controllers\Orders;

use App\Domain\Orders\Order;
use App\Domain\Orders\OrderLine;
use App\Models\Items\ItemManager;
use App\Controllers\Tools\Connect;
use App\Models\Tools\Classes\ConnectDB;
use App\Domain\Accounts\Classes\Customer;
use App\Models\Orders\Classes\OrderManager;
use App\Models\Orders\Classes\OrderLineManager;
use App\Domain\Orders\Exceptions\OrderLineException;
use App\Models\Orders\Classes\SaveOrders\MySQLSaveOrder;
use App\Models\Orders\Classes\SaveOrders\CookiesSaveOrder;

class CartAction
{
    private static OrderManager $manager;

    public static function initCart(): Order
    {
        self::$manager= new OrderManager(new CookiesSaveOrder());

        if(isset($_COOKIE["cart"]))
        {
            $cart = self::$manager->orderSaver()->getCart();
        }

        if(!Connect::typeConnectionVerify('customer'))
        {
            if(isset($cart))
            {
                return $cart;
            }
    
            $cart = new Order([]);
            self::$manager->orderSaver()->add($cart);
            return $cart;
        }


        $customer = new Customer(Connect::getUser());

        self::$manager->orderSaver()->delete(null);
        
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
                if($cartByMySQL->orderLineExist($orderLine))
                {
                    continue;
                }
                $orderLine ->setOrder($cartByMySQL);

                $cartByMySQL->addOrderLine($orderLine);
            }
            $cart = $cartByMySQL;

            self::$manager->orderSaver()->update($cart);
        }

        return $cart;

        

    }


    public static function modifierCart(Order $cart,?Customer $customer,array $dataRequest, string $operation)
    {
        ksort($dataRequest);

        $data = $dataRequest;

        if($operation==='delete')
        {
            self::deleteCart($data,$cart);
        }
        else if($operation==='update')
        {   
            self::updateCart($data,$cart);
        }
        else
        {
            self::addCart($data,$cart);
        }


        self::$manager->orderSaver()->update($cart);

        if(!Connect::typeConnectionVerify('customer'))
        {
            self::$manager= new OrderManager(new CookiesSaveOrder());
            $cart = self::$manager->orderSaver()->getCart();
            return;
        }
        
        self::$manager= new OrderManager(new MySQLSaveOrder(ConnectDB::getInstanceToPDO())); 

        $cart = self::$manager->orderSaver()->getCartByCustomerIfExist($customer);

    }

    private static function deleteCart(array $data, Order $cart)
    {

        $data['id'] = $data['delete'];
        unset($data['delete']);
        $itemManager = new ItemManager(ConnectDB::getInstanceToPDO());
        $data['item'] = $itemManager->getOnce($data['item']);
        $data['order'] = $cart;
        $orderLine = new OrderLine($data);

        if (!$cart->orderLineExist($orderLine)) {
            return;
        }

        if(Connect::typeConnectionVerify('customer'))
        {
            $orderLineManager = new OrderLineManager(ConnectDB::getInstanceToPDO());
            $orderLineManager->delete($orderLine);
        }

        $cart->unsetOrderLine($orderLine,true);

        return;

    }

    private static function updateCart(array $data,Order $cart)
    {
        $itemManager = new ItemManager(ConnectDB::getInstanceToPDO());
        $data['item'] = $itemManager->getOnce($data['item']);
        $data['order'] = $cart;
        $orderLine = new OrderLine($data);
        
        try{
            $cart->setOrderLine($orderLine);
        }
        catch(OrderLineException $e){
            $cart->addOrderLine($orderLine);
        }


    }

    private static function addCart(array $data,Order $cart)
    {
        $itemManager = new ItemManager(ConnectDB::getInstanceToPDO());
        $data['item'] = $itemManager->getOnce($data['item']);
        $data['order'] = $cart;
        
        $orderLine = new OrderLine($data);

        try{
            $cart->addOrderLine($orderLine);
        }
        catch(OrderLineException $e){
            $cart->setOrderLine($orderLine);
        }
    }
}