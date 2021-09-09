<?php

namespace App\Controllers\Orders;

use App\Domain\Orders\Order;
use App\Domain\Orders\FinalOrder;
use App\Models\Tools\Classes\ConnectDB;
use App\Models\Orders\Classes\OrderManager;
use App\Models\Orders\Classes\OrderLineManager;
use App\Models\Orders\Classes\OrderByDistributerManager;
use App\Models\Orders\Classes\SaveOrders\MySQLSaveOrder;
use App\Models\Orders\Classes\Exceptions\MySQLSaveOrderException;
use App\Models\Orders\Classes\Exceptions\OrderLineManagerException;

/**
 * OrderBulider:
 * use to CRUD complet Order
 */
class OrderBuilder
{
    private OrderManager $orderManager;
    private OrderLineManager $orderLineManager;
    private OrderByDistributerManager $orderByDistributerManager;

    public function __construct()
    {
        $connect =ConnectDB::getInstanceToPDO();

        $this->orderManager = new OrderManager(new MySQLSaveOrder($connect));
        $this->orderLineManager = new OrderLineManager(ConnectDB::getInstanceToPDO());
        $this->orderByDistributerManager = new OrderByDistributerManager(ConnectDB::getInstanceToPDO());

    }


    public function add($order): ?Order
    {
        try{
           $this->orderManager->orderSaver()->add($order);
        }

        catch(MySQLSaveOrderException $e)
        {
            echo $e->getMessage();
            return null;
        }


        $cart = $this->orderManager->orderSaver()->getCartByCustomerIfExist($order->customer());
        $cart->setOrderLines($order->orderLines());
        

        foreach ($cart->orderLines()  as $orderLine )
        {
            $orderLine->setOrder($cart);

            try{
                $this->orderLineManager->add($orderLine);
            }
            catch(OrderLineManagerException $e)
            {
                
                return null;
            }
        }
        return $cart;
    }

    public function update (FinalOrder $order): void
    {

        
        try{
            $this->orderManager->orderSaver()->update($order);
        }
        catch(MySQLSaveOrderException $e)
        {
            return;
        }
        
        
        foreach ($order->ordersByDistributer()  as $orderByDistributer )
        {
            $this->orderByDistributerManager->add($orderByDistributer);

        }

        $ordersByDistributer = $this->orderByDistributerManager->getByFinalOrder($order);

        
        
        
        $order->setOrdersByDistributer($ordersByDistributer);

        foreach ($order->ordersByDistributer()  as $orderByDistributer )
        {
            foreach ($orderByDistributer->orderLines() as $orderLine)
            {
                $orderLine->setOrder($orderByDistributer);
                $this->orderLineManager->update($orderLine);
            }
        }
    }
}


