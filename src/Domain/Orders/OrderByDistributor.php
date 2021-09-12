<?php


namespace App\Domain\Orders;

use App\Domain\Orders\Order;
use App\Domain\Orders\OrderLine;
use App\Domain\Accounts\Classes\Distributor;
use App\Domain\Delivery\Classes\AbstractDelivery;
use App\Domain\Orders\Exceptions\OrderByDistributorException;

/**
 * OrderByDistributor
 * 
 * use to represent a order received by a distributor
 * 
 *
 */
final class OrderByDistributor extends cart
{
    private Order $order;
    private Distributor $distributor;
    private AbstractDelivery $delivery;
    private int $status = self::BEING_PROCESSED;

    // CONSTANTS

    const BEING_PROCESSED = 0;
    const BEING_DELIVERED = 1;
    const FINISH = 2;
    const CANCELED = 3;

    // GETTERS    
    /**
     * order
     *
     * return the order object associated
     * 
     * @return Order
     */
    public function order(): Order
    {
        return $this->order;
    }
        
    /**
     * distributor
     * 
     * return the distributor 
     *
     * @return Distributor
     */
    public function distributor(): Distributor
    {
        return $this->distributor;
    }
        
    /**
     * delivery
     *
     * returns the delivery method
     * 
     * @return AbstractDelivery
     */
    public function delivery(): AbstractDelivery
    {
        return $this->delivery;
    }
        
    /**
     * status
     * 
     * use to get status
     *
     * @return int
     */
    public function status(): int
    {
        return $this->status;
    }


    //SETTERS    
    /**
     * setOrder
     *
     * modifie order
     * 
     * @param  Order $order
     * @return void
     */
    public function setOrder($order): void
    {
        if(!is_a($order,get_class(new Order([]))))
        {
            throw new OrderByDistributorException('invalid parameter type',302);
            return;
        }

        $this->order = $order;
    } 
    
    /**
     * setDistributor
     *
     * 
     * 
     * @param  Distributor $distributor
     * @return void
     */
    private function setDistributor($distributor): void
    {
        if(!is_a($distributor,get_class(new Distributor([]))))
        {
            throw new OrderByDistributorException('invalid Argument type',301);
            return;
        }

        $this->distributor = $distributor;

    }

    /**
     * setDelivery
     * 
     * use to modifier delivery
     *
     * @param  AbstractDelivery $delivery
     * @return void
     */
    public function setDelivery($delivery): void
    {
        if(!is_a($delivery,get_class(new AbstractDelivery([]))))
        {
            throw new OrderByDistributorException('invalid Argument type',301);
            return;
        }

        $this->delivery = $delivery;
    }   
    
    /**
     * setStatus
     *
     * modifier status
     * 
     * status get value : self::BEING_DELIVERED, self::BEING_PROCESSED
     * 
     * @param  int $status
     * @return void
     */
    
    public function setStatus($status): void
    {
        if(!in_array( $status, [ self::BEING_PROCESSED, self::BEING_DELIVERED, self::FINISH] ))
        {
            throw new OrderByDistributorException('Invalid status',100);
            return;
        }

        $this->status = $status;

    }

    /**
     * setOrderLines
     * 
     * verifie distributor to item in orderLine for the arderLine array in parameter and modifier OrderLine array
     *
     * @param  array $orderLines
     * @return void
     */
    public function setOrderLines($orderLines):void
    {
        $orderLines = (array) $orderLines;

        if(!isset($this->distributor))
        {
            $this->setDistributor($orderLines[0]->item()->distributor());
        }

        
        $orderLines = array_filter($orderLines, function($orderLine){
            
            if($orderLine->item()->distributor()!== $this->distributor())
            {
                return false;
            }

            return true;
        });
        
        parent::setOrderLines($orderLines);
          
    }

    // OrderLine modifier    
    /**
     * addOrderLine
     *
     * verifie distributor to item in orderLine parameter and add new orderline in orderLines
     * 
     * @param  OrderLine $orderLine
     * @return void
     */
    public function addOrderLine(OrderLine $orderLine): void
    {
        if(empty($this->orderLines()))
        {
            parent::addOrderLine($orderLine);
            
            $this->setDistributor($orderLine->item()->distributor());
            return;
        }

        if($orderLine->item()->distributor()!== $this->distributor())
        {
            throw new OrderByDistributorException('item distributor is diferent');
            return;
        }

        parent::addOrderLine($orderLine);
        
    }

    // get total cost    
    /**
     * getTotalCost
     * 
     * return sum of order lines cost  and delivery price cost 
     *
     * @return int
     */
    public function getTotalCost():int
    {
        return $this->deliveryMethod->price() + parent::getTotalCost();
    }



}