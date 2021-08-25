<?php

namespace App\Models\Orders\Classes\SaveOrders;

use App\Domain\Orders\Order;
use App\Models\Tools\Classes\ConnectDB;
use App\Domain\Accounts\Classes\Customer;
use App\Models\Orders\Classes\OrderLineManager;
use App\Models\Orders\Interfaces\OrderDataSaver;
use App\Models\Orders\Classes\Exceptions\MySQLSaveOrderException;


class MySQLSaveOrder implements OrderDataSaver
{
    private  OrderLineManager $orderLineManager;

    public function __construct(private \PDO $db)
    {
        $this->orderLineManager = new OrderLineManager(ConnectDB::getInstanceToPDO());
    }

    public function getByCustomer(Customer $customer)
    {
        $request = $this-> db -> prepare("SELECT * FROM orders  WHERE customer= :customer ORDER BY id DESC");

        try
        {
            $request ->execute(array(
                "customer" => $customer->id()
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new MySQLSaveOrderException("Recovery orderLine error in the database");
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }

        if($request->rowCount()===0)
        {
            $exception= new MySQLSaveOrderException("Never orderLine to this order");
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }

        $table=[];
        while($data = $request->fetch())
        {
            $data["customer"] = $customer;
            $order = new Order($data);
            $data["orderLines"] = $this->orderLineManager->getByOrder($order);
            $order->setOrderLines($data["orderLines"]);
            $table[]=$order;
        }

        return $table;
    }

    // My SQL
    public function add(Order $order): void
    {
        $request = $this-> db -> prepare("INSERT INTO orders  (customer, status) VALUES (:customer, :status)");
        try
        {
            $request ->execute(array(
                "customer" => $order->customer()->id(),
                "status" => $order->status()
            ));
            
        }

        catch(\PDOException $e)
        {
            $exception= new MySQLSaveOrderException("Recovery orderLine error in the database");
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }

    }

    public function update(Order $order): void
    {
        $request = $this-> db -> prepare("UPDATE orders  SET customer=:customer, status= :status WHERE id= :id");

        try
        {
            $request ->execute(array(
                "id" => $order->id(),
                "customer" => $order->customer()->id(),
                "status" => $order->status()
            ));
            
        }

        catch(\PDOException $e)
        {
            $exception= new MySQLSaveOrderException("Recovery orderLine error in the database");
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
        
    }

    public function delete(?Order $order): void
    {
        $request = $this-> db -> prepare("DELETE FROM orders WHERE id= :id");

        try
        {
            $request ->execute(array(
                "id" => $order->id()
            ));
            
        }

        catch(\PDOException $e)
        {
            $exception= new MySQLSaveOrderException("Recovery orderLine error in the database");
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
    }

}