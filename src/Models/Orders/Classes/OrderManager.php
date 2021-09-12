<?php

namespace App\Models\Orders\Classes;

use App\Domain\Orders\Order;
use App\Domain\Accounts\Classes\Customer;
use App\Domain\Orders\OrderByDistributor;
use App\Models\Accounts\CustomerManager;
use App\Models\Orders\Classes\Exceptions\OrderManagerException;

class OrderManager
{
    private CustomerManager $customerManager;
    public function __construct(private \PDO $db)
    {
        $this->customerManager = new CustomerManager($this->db);
    }


    
    // CRUD operations
    //to GET
    /**
     * getOnce
     *
     * permit to get specific order
     * 
     * @param  int $id
     * @return order
     */
    public function getOnce(int $id):?order
    {
        $request = $this->db -> prepare('SELECT* FROM orders WHERE id = :id');

        try{
            $request->execute(array(
                "id"=>$id
            )); 
        }
        catch (\PDOException $e)
        {
            $exception= new OrderManagerException("Recovery order error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            throw new OrderManagerException("Order not found",500);
            return null;
        }
        $data = $request->fetch(\PDO::FETCH_ASSOC);
        $data['customer'] = $this-> customerManager->getOnce($data['customer']); 
        return new Order($data);
    }
  
    /**
     * getAllByCustomer
     * 
     * permit to get all order to the customer
     *
     * @param  Customer $customer
     * @return array
     */
    public function getAllByCustomer(Customer $customer): ?array
    {
        $request = $this-> db -> prepare('SELECT * FROM orders WHERE customer =:customer ORDER BY id DESC');

        try{
            $request->execute(array(
                "customer"=>$customer-> id()
            )); 
        }
        catch (\PDOException $e)
        {
            $exception= new OrderManagerException("Recovery order error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            throw new OrderManagerException("Order not found",500);
            return null;
        }

        $result = [];
        while ($data = $request->fetch()) {
            $result[] = new Order($data);
        }

        return $result;

    }
    
    /**
     * getLastByCustomer
     * 
     * permit to get last order add in database
     *
     * @param  Customer $customer
     * @return Order
     */
    public function getLastByCustomer(Customer $customer): ?Order
    {
        $request = $this-> db -> prepare('SELECT * FROM orders WHERE customer =:customer ORDER BY id DESC LIMIT 1');

        try{
            $request->execute(array(
                "customer"=>$customer-> id()
            )); 
        }
        catch (\PDOException $e)
        {
            $exception= new OrderManagerException("Recovery order error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            throw new OrderManagerException("Order not found",500);
            return null;
        }

        return new Order($request->fetch(\PDO::FETCH_ASSOC));

    }
    
    /**
     * getByStatusAndCustomer
     * 
     * permit to select order by status
     *
     * @param  Customer $customer
     * @param  int $status
     * @return array
     */
    public function getByStatusAndCustomer(Customer $customer,int $status): ?array
    {
        if(!in_array($status,OrderByDistributor::STATUS_LIST))
        {
            throw new OrderManagerException("status not exist",101);
            return null;
        }

        $request = $this-> db -> prepare('SELECT * FROM orders WHERE customer=:customer AND `status`=:status ORDER BY id DESC');

        try{
            $request->execute(array(
                "customer"=>$customer-> id()
            ));
        }
        catch (\PDOException $e)
        {
            $exception= new OrderManagerException("Recovery order error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            throw new OrderManagerException("Order not found",500);
            return null;
        }

        $result = [];
        while ($data = $request->fetch()) {
            $result[] = new Order($data);
        }

        return $result;
    }

    // to add
    /**
     * add
     *
     * permit to add new order in database
     * 
     * @param  Order $order
     * @return void
     */
    public function add(Order $order): void
    {
        $request = $this-> db->prepare("INSERT INTO orders (customer, `status`) VALUES (:customer, :status)");

        try
        {
            $request->execute(array(
                'customer'=>$order->customer()->id(),
                'status'=>$order->status()
            ));
        }
        catch(\PDOException $e)
        {
            $exception= new OrderManagerException("Recovery order error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }

    }
    
    //to update
    /**
     * update
     * 
     * pemit to modifier order in database
     *
     * @param  Order $order
     * @return void
     */
    public function update(Order $order): void
    {
        $request = $this-> db->prepare("UPDATE orders SET `status`=:status WHERE id = :id");

        try
        {
            $request->execute(array(
                'id'=>$order->id(),
                'status'=>$order->status()
            ));
        }
        catch(\PDOException $e)
        {
            $exception= new OrderManagerException("Recovery order error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        } 
    }

}