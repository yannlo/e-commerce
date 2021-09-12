<?php

namespace App\Models\Orders\Classes;

use App\Models\Orders\Interfaces\CartCRUD;
use App\Models\Orders\Classes\SaveCarts\MySQL;
use App\Models\Orders\Classes\SaveCarts\Cookies;


class CartManager
{
    public function __construct(private ?CartCRUD $cartCRUD)
    { }

    public function setCartSaver(CartCRUD $cartCRUD)
    {
        $this->cartCRUD = $cartCRUD;
    }

    public function cartSaver(): MySQL|Cookies
    {
       return $this->cartCRUD;
    }

}