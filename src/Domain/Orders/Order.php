<?php

namespace App\Domain\Orders;

use App\Domain\Orders\Exceptions\OrderException;
use App\Domain\Orders\Exceptions\OrderByDistributorException;

/**
 * Order
 * 
 * use to represent Order in database
 * 
 */
final class Order extends AbstractOrder
{

    //variables
    private array $ordersByDistributor=[];
    private int $status;

    // GETTERS    
    /**
     * ordersByDistributor
     * 
     * get orderByDistributor array
     *
     * @return array
     */
    public function ordersByDistributor(): array
    {
        return $this->ordersByDistributor;
    }
    
    /**
     * status
     *
     * get status value
     * 
     * @return int
     */
    public function status(): int
    {
        return $this->status;
    }


    // SETTERS    
    /**
     * setOrdersByDistributor
     * 
     * set orderByDistributor array
     *
     * @param  array $ordersByDistributor
     * @return void
     */
    public function setOrdersByDistributor(array $ordersByDistributor): void
    {
        if (empty($ordersByDistributor))
        {
            throw new OrderException('ordersByDistributer is empty',200);
            return;
        }

        foreach($ordersByDistributor as $orderByDistributor)
        {
            if(!is_a($orderByDistributor,get_class(new OrderByDistributor([]))))
            {
                throw new OrderException('incorrect value in array',302);
                return;

                $orderByDistributor->setOrder($this);

            }
        }
        
        $this->ordersByDistributor = $ordersByDistributor;
    }
    
    /**
     * setStatus
     *
     * set status value
     * 
     * @param  int $status
     * @return void
     */
    private function setStatus($status):void
    {
        $status = (int)$status;

        $this->status = $status;
    }


    // metthod
    // status method    
    /**
     * updateStatus
     * 
     * use to modifier order status 
     * 
     * it select the minimum status value and mofifier the order status with this value
     *
     * @return void
     */
    public function updateStatus(): void
    {
        $stateList = array();
        foreach($this->ordersByDistributor() as $orderByDistributor)
        {
            $stateList[]= $orderByDistributor->status();
        }

        $this->setStatus(min($stateList));
    }

    //  get total cost    
    /**
     * getTotalCost
     * 
     * return the addition of the entire order by distributor
     *
     * @return int
     */
    public function getTotalCost(): int
    {
        $cost=0;
        foreach($this->ordersByDistributor as $orderByDistribut)
        {
            $cost+=$orderByDistribut->getTotalCost();
        }
        return $cost;
    }

}
