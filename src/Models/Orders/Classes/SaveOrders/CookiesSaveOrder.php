<?php

namespace App\Models\Orders\Classes\SaveOrders;

use App\Domain\Orders\Order;
use App\Domain\Tools\JSONFormatter;
use App\Models\Orders\Interfaces\OrderDataSaver;
use App\Models\Orders\Classes\Exceptions\CookiesSaveOrderException;


class CookiesSaveOrder implements  OrderDataSaver
{
    public function getCart()
    {
        return JSONFormatter::jsonDecoderToOrder($_COOKIE["cart"]);
    }

    public function add(Order $order): void
    {
        if($order->status() !== Order::CART)
        {
            throw new CookiesSaveOrderException("Order status is not cart");
            return;
        }

        $stringOrder = JSONFormatter::jsonEncoderToOrder($order);

        $resultCookies=setcookie(
            name:"cart",
            value: $stringOrder,
            expires_or_options: time() + (60 * 60 * 24*31)
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


        if(!CookiesSaveOrder::cartExist())
        {
            throw new CookiesSaveOrderException("cart cookies not exist");
            return;
        }

        if($order->status() !== Order::CART)
        {
            throw new CookiesSaveOrderException("Order status is not cart");
            return;
        }

        $stringOrder = JSONFormatter::jsonEncoderToOrder($order);

        $resultCookies=setcookie(
            name:"cart",
            value: $stringOrder,
            expires_or_options: time() + (60 * 60 * 24*31)
        );

        $_COOKIE["cart"]= $stringOrder;

        if($resultCookies===false)
        {
            throw new CookiesSaveOrderException("Cookies is not saved");
            return;
        }

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

    public static function cartExist()
    {
        if(!isset($_COOKIE["cart"]) || empty($_COOKIE["cart"]))
        {
            return false;
        }

        return true;
    }
}