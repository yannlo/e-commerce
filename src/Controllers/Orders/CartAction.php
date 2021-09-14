<?php

namespace App\Controllers\Orders;

use App\Domain\Orders\Cart;
use App\Domain\Orders\Order;
use App\Domain\Orders\OrderLine;
use App\Models\Items\ItemManager;
use App\Controllers\Tools\Connect;
use App\Models\Tools\Classes\ConnectDB;
use App\Domain\Accounts\Classes\Customer;
use App\Domain\Orders\OrderByDistributor;
use App\Models\Orders\Classes\SaveCarts\MySQL;
use App\Domain\Orders\Exceptions\CartException;
use App\Models\Orders\Classes\SaveCarts\Cookies;
use App\Models\Orders\Classes\Builders\CartBuider;


class CartAction
{
    private static CartBuider $cartManager;
    
    /**
     * initialization
     * 
     * permit to initialize cart for the customer
     *
     * @return Cart
     */
    public static function initialization(): Cart
    {
        self::$cartManager = new CartBuider(new Cookies);

        $cartSaver = self::$cartManager->cartSaver();

        if($cartSaver->cartExist())
        {
            $cartInCookies = $cartSaver->get();
        }


        $isConneted= Connect::typeConnectionVerify('customer');
        
        if($isConneted)
        {
            self::$cartManager->setCartSaver(new MySQL(ConnectDB::getInstanceToPDO()));

            $cartSaver = self::$cartManager->cartSaver();

            $customer =Connect::getUser();

            if($cartSaver->cartExist($customer))
            {
                $cartInMySql = $cartSaver->get($customer);
            }
        }
 

        if(!isset($cartInCookies) && !$isConneted)
        {
            $cart = new Cart([]);

            self::$cartManager->setCartSaver(new Cookies);

            $cartSaver = self::$cartManager->cartSaver();
                
            $cartSaver->add($cart);

            $cart = $cartSaver->get();
            
        }
        else if(isset($cartInCookies) && $isConneted)
        {

            self::$cartManager->setCartSaver(new Cookies);

            $cartSaver = self::$cartManager->cartSaver();

            $cartSaver->delete();

            if(!isset($cartInMySql))
            {
                self::$cartManager->setCartSaver(new MySQL(ConnectDB::getInstanceToPDO()));

                $cartSaver = self::$cartManager->cartSaver();

                $cartInCookies->setCustomer($customer);

                $cartSaver->add($cartInCookies);

                $cart = $cartSaver->get($customer);
            }
            else
            {
                
                foreach($cartInCookies->orderLines() as $orderLine)
                {
                    if(!$cartInMySql->orderLineExist($orderLine))
                    {
                        $cartInMySql->addOrderLine($orderLine);
                    }
                }
    
                self::$cartManager->setCartSaver(new MySQL(ConnectDB::getInstanceToPDO()));
    
                $cartSaver = self::$cartManager->cartSaver();
                
                $cartSaver->update($cartInMySql);
    
                $cart = $cartInMySql;

            }


        }
        else if(isset($cartInCookies) && !$isConneted)
        {
            $cart = $cartInCookies;
        }
        else if(!isset($cartInCookies) && $isConneted)
        {
            
            if(!isset($cartInMySql))
            {
                self::$cartManager->setCartSaver(new MySQL(ConnectDB::getInstanceToPDO()));

                $cartSaver = self::$cartManager->cartSaver();

                $cart = new Cart([
                    "customer"=> $customer
                ]);

                $cartSaver->add($cart);

                $cart = $cartSaver->get($customer);
            }
            else
            {
                $cart = $cartInMySql;
            }

        }
        return $cart;
    }
    
    /**
     * update
     * 
     * use to update cart orderLine
     *
     * @param  Cart $cart
     * @param  array $data
     * @return void
     */
    public static function update(Cart $cart, array $data): void
    {
        ksort($data);
        unset($data['submit']);
        $data['id']= 0;
        $data['cart']= $cart;
        
        if(!empty($data['deleteItem']))
        {
            $data["item"] = (new ItemManager(ConnectDB::getInstanceToPDO()))->getOnce($data["deleteItem"]);
            $orderLine = new OrderLine($data);
            $cart->deleteOrderLine($orderLine,true);
            
        }
        else
        {
            $data["item"] = (new ItemManager(ConnectDB::getInstanceToPDO()))->getOnce($data["item"]);
            
            $orderLine = new OrderLine($data);

            try
            
            {
                $cart->addOrderLine($orderLine);
                
            }
            catch(CartException)
            {
                $orderLineFound = $cart -> foundOrderLine($orderLine);
                $orderLineFound ->setQuantity($orderLine->quantity());
            }
            
            
        }
        
        self::$cartManager = new CartBuider(new Cookies);
        
        $cartSaver = self::$cartManager->cartSaver();
        
        if(Connect::typeConnectionVerify('customer'))
        {
            self::$cartManager= new CartBuider(new MySQL(ConnectDB::getInstanceToPDO()));
            
            $cartSaver = self::$cartManager->cartSaver();
        }
        $cartSaver->update($cart);

    }
    
    /**
     * convertToOrder
     * 
     * permit to convert cart to order
     *
     * @param  Cart $cart
     * @return Order
     */
    public static function convertToOrder(Cart $cart): Order
    {   
        $order = new Order([
            "customer" => $cart->customer()
        ]);

        $ordersByDistributor= [];
        foreach($cart->orderLines() as $orderLine)
        {
            if(empty($ordersByDistributor))
            {
                $orderByDistributor =new OrderByDistributor([
                    "order" => $order,
                    "customer" => $order-> customer()
                ]);
    
                $orderLine ->setOrderByDistributor($orderByDistributor);
                $orderByDistributor->addOrderLine($orderLine);
                    
                $cart->deleteOrderLine($orderLine,true);
    
                $ordersByDistributor[] = $orderByDistributor;
                
                continue;
            }

            foreach($ordersByDistributor as $orderByDistributor)
            {
                if($orderByDistributor->distributor() !== $orderLine->item()->distributor())
                {
                    continue;
                }
                $orderLine ->setOrderByDistributor($orderByDistributor);
                
                $orderByDistributor->addOrderLine($orderLine);

                $cart->deleteOrderLine($orderLine,true);
                break;
            }

            if(isset($orderLine))
            {
                $orderByDistributor =new OrderByDistributor([
                    "order" => $order,
                    "customer" => $order-> customer()
                ]);
                
                $orderLine ->setOrderByDistributor($orderByDistributor);
                $orderByDistributor->addOrderLine($orderLine);
                
                $cart->deleteOrderLine($orderLine,true);

                $ordersByDistributor[] = $orderByDistributor;
            }

        }

        $order->setOrdersByDistributor($ordersByDistributor);

        return $order ;
    
    }
}