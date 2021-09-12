<?php

namespace App\Models\Orders\Classes\InMySQL;

use App\Domain\Orders\Order;
use App\Domain\Orders\OrderByDistributor;
use App\Models\Accounts\DistributorManager;
use App\Models\Orders\Classes\OrderManager;
use App\Domain\Accounts\Classes\Distributor;
use App\Models\Orders\Classes\Exceptions\OrderByDistributorManagerException;

class OrderByDistributorManager
{
    private DistributorManager $distributorManager;
    private OrderManager $orderManager;
    
    public function __construct(private \PDO $db)
    { 
        $this->distributorManager = new DistributorManager($this->db);
        $this->orderManager = new OrderManager($this->db);
    }

    
    // CRU
    //get
    /**
     * getOnce
     * 
     * permit to get specific order by distributor
     *
     * @param  int $id
     * @return OrderByDistributor
     */
    public function getOnce(int $id): ?OrderByDistributor
    {
        $request = $this-> db -> prepare('SELECT * FROM ordersbydistributor WHERE `id` = :id');
        try
        {
            $request -> execute(array(
                'id' => $id
            ));
        }
        catch (\PDOException $e)
        {
            $exception= new OrderByDistributorManagerException("Recovery orderByDIstributor error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            $exception= new OrderByDistributorManagerException("Never orderByDIstributor to this order",500);
            throw $exception;
            return null;
        }

        $data = $request->fetch();

        $data['order'] = $this->orderManager->getOnce($data['order']);
        $data['customer'] = $data['order']->customer();
        $data['distributor'] = $this->distributorManager->getOnce($data['distributor']);
         

        return new OrderByDistributor($data);

    }
    
    /**
     * getByOrder
     *
     * permit to get all orderByDistributor by order
     * 
     * @param  Order $order
     * @return array
     */
    public function getByOrder(Order $order): ?array
    {
        $request = $this-> db -> prepare('SELECT * FROM ordersbydistributor WHERE `order` = :order');
        try
        {
            $request -> execute(array(
                'order' => $order->id()
            ));
        }
        catch (\PDOException $e)
        {
            $exception= new OrderByDistributorManagerException("Recovery orderByDIstributor error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            $exception= new OrderByDistributorManagerException("Never orderByDIstributor to this order",500);
            throw $exception;
            return null;
        }

        $ordersByDistributor=[];

        while ($data = $request->fetch())
        {
            $data['order'] = $order;
            $data['customer'] = $order->customer();
            $data['distributor'] = $this->distributorManager->getOnce($data['distributor']);
            $ordersByDistributor[] = new OrderByDistributor($data);
        }

        return $ordersByDistributor;

    }
    
    /**
     * getAllByDistributor
     * 
     * permit to get orderByDistributor by distributor
     *
     * @param  Distributor $distributor
     * @return array
     */
    public function getAllByDistributor(Distributor $distributor): ?array
    {
        $request = $this-> db -> prepare('SELECT * FROM ordersbydistributor WHERE `distributor` = :distributor ORDER BY id DESC');
        try
        {
            $request -> execute(array(
                'distributor' => $distributor->id()
            ));
        }
        catch (\PDOException $e)
        {
            $exception= new OrderByDistributorManagerException("Recovery orderByDIstributor error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            $exception= new OrderByDistributorManagerException("Never orderByDIstributor to this distributor",500);
            throw $exception;
            return null;
        }

        $ordersByDistributor=[];
        
        while ($data = $request->fetch())
        {
            $data['distributor'] = $distributor;
            $data['order'] = $this->orderManager->getOnce($data['order']);
            $data['customer'] = $data['order']->customer();
            $ordersByDistributor[] = new OrderByDistributor($data);
        }
        return $ordersByDistributor;

    }

    /**
     * getByStatusAndDistributor
     * 
     * permit to select order by status
     *
     * @param  Distributor $distributor
     * @param  int $status
     * @return array
     */
    public function getByStatusAndDistributor(Distributor $distributor,int $status): ?array
    {
        if(!in_array($status,OrderByDistributor::STATUS_LIST))
        {
            throw new OrderByDistributorManagerException("status not exist",101);
            return null;
        }

        $request = $this-> db -> prepare('SELECT * FROM ordersbydistributor WHERE distributer=:distributer AND `status`=:status ORDER BY id DESC');

        try{
            $request->execute(array(
                "distributer"=>$distributor-> id()
            ));
        }
        catch (\PDOException $e)
        {
            $exception= new OrderByDistributorManagerException("Recovery order by distributor error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            throw new OrderByDistributorManagerException("Order not found",500);
            return null;
        }

        $result = [];

        while ($data = $request->fetch())
        {
            $data['distributor'] = $distributor;
            $data['order'] = $this->orderManager->getOnce($data['order']);
            $data['customer'] = $data['order']->customer();
            $ordersByDistributor[] = new OrderByDistributor($data);
        }

        return $result;
    }


    // add  
    /**
     * add
     * 
     * permit to add new order by distributor
     *
     * @param  OrderByDistributor $orderByDistributor
     * @return void
     */
    public function add(OrderByDistributor $orderByDistributor): void
    {
        $request = $this->db -> prepare('INSERT INTO ordersbydistributor (distributor,status,`order`) VALUES (:distributor, :status,:order)');
        try{

            $request->execute(array(
                'distributor' => $orderByDistributor->distributor()->id(),
                'status' => $orderByDistributor->status(),
                'order' => $orderByDistributor->order()->id()
            ));
        }
        catch (\PDOException $e)
        {
            $exception= new OrderByDistributorManagerException("Recovery orderByDIstributor error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }

    }

    //update
    /**
     * update
     *
     * permit to update order by distributor
     * 
     * @param  OrderByDistributor $orderByDistributor
     * @return void
     */
    public function update(OrderByDistributor $orderByDistributor,): void
    {
        $request = $this->db -> prepare('UPDATE ordersByDistributor SET distributor=:distributor, status =:status WHERE id=:id ');

        try{

            $request->execute(array(
                'id' => $orderByDistributor->id(),
                'distributor' => $orderByDistributor->distributor()->id(),
                'status' => $orderByDistributor->status()
            ));
        }
        catch (\PDOException $e)
        {
            $exception= new OrderByDistributorManagerException("Recovery orderByDIstributor error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
    }

    

}