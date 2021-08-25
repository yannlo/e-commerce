<?php

namespace App\Models\Orders\Classes;

use App\Models\Orders\Interfaces\OrderDataSaver;

class OrderManager
{
    public function __construct(private OrderDataSaver $OrderSaver)
    { }

    public function setOrderSaver(OrderDataSaver $OrderSaver)
    {
        $this->OrderSaver = $OrderSaver;
    }

}