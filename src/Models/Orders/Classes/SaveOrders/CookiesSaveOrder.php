<?php

namespace App\Models\Orders\Classes\SaveOrders;

use App\Domain\Orders\Order;
use App\Models\Orders\Interfaces\OrderDataSaver;
use App\Models\Orders\Classes\Exceptions\CookiesSaveOrderException;


class CookiesSaveOrder implements  OrderDataSaver
{
    public function getCart()
    {
        return unserialize($_COOKIE["cart"]);
    }

    public function add(Order $order): void
    {
        if($order->status !== Order::CART)
        {
            throw new CookiesSaveOrderException("Order status is not cart");
            return;
        }

        $stringOrder = serialize($order);

        $resultCookies=setcookie(
            name:"cart",
            value: $stringOrder,
            expires_or_options: time() + (60 * 60 * 24)
        );

        $_COOKIE["cart"]= $stringOrder;

        if($resultCookies===false)
        {
            throw new CookiesSaveOrderException("Cookies is not saved");
            return;
        }


    }

    public function update(Order $order): void
    {

        if(isset($_COOKIE["cart"])OR empty($_COOKIE["cart"]))
        {
            throw new CookiesSaveOrderException("cart cookies not exist");
            return;
        }

        if($order->status !== Order::CART)
        {
            throw new CookiesSaveOrderException("Order status is not cart");
            return;
        }

        $stringOrder = serialize($order);
        $_COOKIE["cart"]= $stringOrder;

    }

    public function delete(?Order $order): void
    {
        $resultCookies=setcookie(
            name:"cart",
            value: "",
            expires_or_options: time()-10
        );

        if($resultCookies===false)
        {
            throw new CookiesSaveOrderException("Cookies is not delete");
            return;
        }

        unset($_COOKIE["cart"]);
    }
}