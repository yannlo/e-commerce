<?php

namespace App\Models\Orders\Classes;

use App\Models\Orders\Interfaces\OrderDataSaver;
use App\Models\Orders\Classes\SaveOrders\MySQLSaveOrder;
use App\Models\Orders\Classes\SaveOrders\CookiesSaveOrder;

class OrderManager
{
    public function __construct(private ?OrderDataSaver $OrderSaver)
    { }

    public function setOrderSaver(OrderDataSaver $OrderSaver)
    {
        $this->OrderSaver = $OrderSaver;
    }

    public function orderSaver(): MySQLSaveOrder|CookiesSaveOrder|null
    {
       return $this->OrderSaver;
    }

}