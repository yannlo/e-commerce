<?php

namespace App\Models\Orders\Classes\SaveOrders;

use App\Domain\Orders\Order;
use App\Domain\Orders\FinalOrder;
use App\Models\Tools\Classes\ConnectDB;
use App\Domain\Accounts\Classes\Customer;
use App\Models\Orders\Classes\OrderLineManager;
use App\Models\Orders\Interfaces\OrderDataSaver;
use App\Models\Orders\Classes\OrderByDistributerManager;
use App\Models\Orders\Classes\Exceptions\MySQLSaveOrderException;
use App\Models\Orders\Classes\Exceptions\OrderLineManagerException;


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
            // $exception= new MySQLSaveOrderException("Never orderLine to this order");
            // throw $exception;
            return false;
        }

        $table=[];
        while($data = $request->fetch())
        {
            $data["customer"] = $customer;
            $table[]=$data;
        }

        return $table;
    }

    public function getCartByCustomerIfExist(Customer $customer)
    {

        $dataset =$this -> getByCustomer($customer);

        if(!is_array($dataset))
        {
            return false;
        }
        foreach($dataset as $data)
        {
            if($data['status']== Order::CART)
            {
                $order = new Order($data);
                return $order;  
            }
        }

        return false;    
    }

    public function getOnlyOrderByCustomer(Customer $customer)
    {
        $dataset = array_filter($this -> getByCustomer($customer),function($data){

            if($data['status']=== Order::CART)
            {
                return false;
            }
            
            return true;
        });
        $orders =[];

        foreach($dataset as $data)
        {
            $order = new FinalOrder($data);
            $orders[]=$order;
        }
        return $orders;  
    }

    public function getOrderInProcessByCustomer(Customer $customer)
    {
        $orders = array_filter($this -> getOnlyOrderByCustomer($customer),function($order){
            if(in_array($order->status(), [Order::FINISH]))
            {
                return false;
            }
            return true;
        });
        return $orders;
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
            dd($e->getMessage());
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