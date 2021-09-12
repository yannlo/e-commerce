<?php

namespace App\Models\Orders\Classes\Builders;

use App\Domain\Orders\Order;
use App\Domain\Orders\OrderByDistributor;
use App\Domain\Accounts\Classes\Distributor;
use App\Models\Orders\Classes\OrderLineManager;
use App\Models\Orders\Classes\InMySQL\OrderByDistributorManager;
use App\Models\Orders\Classes\Exceptions\OrderLineManagerException;

class OrderByDistributorBuider
{
    private OrderByDistributorManager $orderByDistributorManager;
    private OrderLineManager $orderLineManager;
    
    public function __construct(private \PDO $db)
    {
        $this->orderByDistributorManager = new OrderByDistributorManager($this->db);
        $this->orderLineManager = new OrderLineManager($this->db);
    }
    


    // CRUD
    // get
    /**
     * getOnce
     * 
     * get conmplete orderByDistributor
     *
     * @param  mixed $id
     * @return OrderByDistributor
     */
    public function getOnce(int $id): ?OrderByDistributor
    {
        $orderByDistributor = $this->orderByDistributorManager->getOnce($id);
        
        try{
            $orderlines=$this->orderLineManager->getByOrderByDistributor($orderByDistributor);
            $orderByDistributor->setOrderLines($orderlines);
        }
        catch(OrderLineManagerException $e)
        {
            if ($e->getCode() !== 500)
            {
                throw $e;
                return null;
            }
        }

        return $orderByDistributor;

    }

    /**
     * getByOrder
     *
     * permit to get complet orderByDistributor by Order
     * 
     * @param  Order $order
     * @return array
     */
    public function getByOrder(Order $order): ?array
    {
        $ordersByDistributor = $this -> orderByDistributorManager -> getByOrder($order);
        foreach ($ordersByDistributor as $orderByDistributor) 
        {
            try{
                $orderlines=$this->orderLineManager->getByOrderByDistributor($orderByDistributor);
                $orderByDistributor->setOrderLines($orderlines);
            }
            catch(OrderLineManagerException $e)
            {
                if ($e->getCode() !== 500)
                {
                    throw $e;
                    return null;
                }
            }  
        }

        return $ordersByDistributor;
    }
    
    /**
     * getAllByDistributor
     * 
     * get all complet orderByDistributor by distributor
     *
     * @param  Distributor $distributor
     * @return array
     */
    public function getAllByDistributor(Distributor $distributor): ?array
    {
        $ordersByDistributor = $this -> orderByDistributorManager -> getAllByDistributor($distributor);
        foreach ($ordersByDistributor as $orderByDistributor) 
        {
            try{
                $orderlines=$this->orderLineManager->getByOrderByDistributor($orderByDistributor);
                $orderByDistributor->setOrderLines($orderlines);
            }
            catch(OrderLineManagerException $e)
            {
                if ($e->getCode() !== 500)
                {
                    throw $e;
                    return null;
                }
            }  
        }

        return $ordersByDistributor;
    }
    
    /**
     * getByStatusAndDistributor
     * 
     * get complet orderByDistributor by status and distributor
     *
     * @param  Distributor $distributor
     * @param  int $status
     * @return array
     */
    public function getByStatusAndDistributor(Distributor $distributor,int $status): ?array
    {
        $ordersByDistributor = $this -> orderByDistributorManager -> getByStatusAndDistributor($distributor,$status);
        foreach ($ordersByDistributor as $orderByDistributor) 
        {
            try{
                $orderlines=$this->orderLineManager->getByOrderByDistributor($orderByDistributor);
                $orderByDistributor->setOrderLines($orderlines);
            }
            catch(OrderLineManagerException $e)
            {
                if ($e->getCode() !== 500)
                {
                    throw $e;
                    return null;
                }
            }  
        }

        return $ordersByDistributor;
    }

    // add    
    /**
     * add
     * 
     * permit to add complet orderByDistributor
     *
     * @param  mixed $orderByDistributor
     * @return void
     */
    public function add(OrderByDistributor $orderByDistributor): void
    {
        $this->orderByDistributorManager->add($orderByDistributor);

        foreach($orderByDistributor->orderLines() as $orderLine)
        {
            $this->orderLineManager->add($orderLine);
        }
    }

    // update
    public function update(OrderByDistributor $orderByDistributor): void
    {
        $this->orderByDistributorManager ->update($orderByDistributor);
    }




}