<?php

namespace App\Controllers\Orders;

use App\Domain\Orders\Cart;
use App\Domain\Orders\OrderLine;
use App\Models\Items\ItemManager;
use App\Controllers\Tools\Connect;
use App\Models\Tools\Classes\ConnectDB;
use App\Domain\Accounts\Classes\Customer;
use App\Models\Orders\Classes\CartManager;
use App\Models\Orders\Classes\SaveCarts\MySQL;
use App\Domain\Orders\Exceptions\CartException;
use App\Models\Orders\Classes\SaveCarts\Cookies;


class CartAction
{
    private static CartManager $cartManager;

    public static function initialization(): Cart
    {
        self::$cartManager = new CartManager(new Cookies);

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

            $customer = new Customer(Connect::getUser());

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

            $cart = $cartSaver->get($customer);
            
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

    public static function update(Cart $cart, array $data)
    {
        ksort($data);
        unset($data['submit']);
        $data['id']= 0;

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
        
        self::$cartManager = new CartManager(new Cookies);
        
        $cartSaver = self::$cartManager->cartSaver();
        
        if(Connect::typeConnectionVerify('customer'))
        {
            self::$cartManager= new CartManager(new MySQL(ConnectDB::getInstanceToPDO()));
            
            $cartSaver = self::$cartManager->cartSaver();
        }
        $cartSaver->update($cart);

    }
}