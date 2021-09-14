<?php

namespace App\Controllers\Orders;

use App\Views\Orders\CartViews;


class CartController
{
    
    /**
     * index
     * 
     * get cart by customer
     *
     * @return void
     */
    public static function index():void
    {

        $cart = CartAction::initialization();


        if(!empty($_POST))
        {
            CartAction::update($cart, $_POST);
        }

        if(!empty($_GET))
        {
            CartAction::update($cart, $_GET);
        }


        CartViews::index($cart);

    }
    
    /**
     * confirm
     * 
     * permit to confirm cart
     *
     * @param  string $section
     * @return void
     */
    public static function confirm(string $section):void
    {
        $cart = CartAction::initialization();
        $order = CartAction::convertToOrder($cart);

    }

}