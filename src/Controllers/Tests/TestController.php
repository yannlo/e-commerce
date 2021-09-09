<?php

namespace App\Controllers\Tests;

use App\Domain\Orders\Order;
use App\Domain\Orders\OrderLine;
use App\Domain\Orders\FinalOrder;
use App\Models\Items\ItemManager;
use App\Controllers\Tools\Connect;
use App\Domain\Items\Classes\Item;
use App\Controllers\Orders\CartAction;
use App\Models\Tools\Classes\ConnectDB;
use App\Controllers\Orders\OrderBuilder;
use App\Models\Accounts\CustomerManager;
use App\Domain\Accounts\Classes\Customer;
use App\Domain\Orders\OrderByDistributer;
use App\Models\Accounts\DistributerManager;
use App\Domain\Accounts\Classes\Distributer;
use App\Models\Orders\Classes\OrderByDistributerManager;


class TestController
{
    public static function order()
    {     
        $customer =new Customer(Connect::getUser());
        $items = (new ItemManager(ConnectDB::getInstanceToPDO()))->getAll();
        
        foreach($items as $item)
        {
            $item->setDistributer((new DistributerManager(ConnectDB::getInstanceToPDO()))->getByItem($item));
        }

        $orderLine1 = new OrderLine([
            "id" => 1,
            "item" => $items[0],
            "quantity" => 2

        ]);

        $orderLine2 = new OrderLine([
            "id" => 2,
            "item" => $items[1],
            "quantity" => 1

        ]);

        $orderLine3 = new OrderLine([
            "id" => 3,
            "item" => $items[2],
            "quantity" => 5

        ]);

        $cart = new Order(array(
            "id" => 1,
            "customer"=> $customer,
            "orderLines"=> [$orderLine1, $orderLine3],
            "status" => Order::CART
        ));

        $orderLine1->setOrder($cart);
        $orderLine2->setOrder($cart);
        $orderLine3->setOrder($cart);


        $orderBuilder = new OrderBuilder();

        $cart = $orderBuilder->add($cart);

        $finalOrder = new FinalOrder(array(
            "id" => $cart->id(),
            "customer"=>  $cart->customer(),
            "orderLines"=> $cart->orderLines()
        ));
        $orderBuilder->update($finalOrder);
        
        echo "end";



        

    }
}