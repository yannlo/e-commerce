<?php

namespace App\Controllers\Orders;

use App\Views\Orders\CartViews;


class CartController
{

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

}