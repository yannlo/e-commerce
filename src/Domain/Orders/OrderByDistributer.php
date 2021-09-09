<?php


namespace App\Domain\Orders;

use App\Domain\Orders\Order;
use App\Domain\Orders\OrderLine;
use App\Domain\Orders\FinalOrder;
use App\Domain\Accounts\Classes\Distributer;
use App\Domain\Orders\Exceptions\OrderByDistributerException;
use App\Domain\Factory\DeliveryFactory\Interfaces\DeliveryFactoryInterface;

final class OrderByDistributer extends Order
{
    private Distributer $distributer;
    private FinalOrder $order;
    private DeliveryFactoryInterface $deliveryMethod;

    protected int $status = self::BEING_PROCESSED;

    public function order(): FinalOrder
    {
        return $this->order;
    }

    public function setOrder($order): void
    {
        $this->order = $order;
    }


    public function setOrderLines(array $orderLines):void
    {
        if(!isset($this->distributer))
        {
            $this->distributer =$orderLines[0]->item()->distributer();
        }

        
        $orderLines = array_filter($orderLines, function($orderLine){
            
            if($orderLine->item()->distributer()->id()!== $this->distributer->id())
            {
                return false;
            }

            return true;
        });

        
        parent::setOrderLines($orderLines);
          
    }    
    public function addOrderLine(OrderLine $orderLine): void
    {
        if(empty($this->orderLines))
        {
            parent::addOrderLine($orderLine);
            $this->distributer = $orderLine->item()->distributer();
            return;
        }

        if($orderLine->item()->distributer()->id()!== $this->distributer->id())
        {
            throw new OrderByDistributerException('item distributer is diferent');
            return;
        }

        parent::addOrderLine($orderLine);
        
    }

    public function getTotalCost():int
    {
        // recupere le prix de la livraison
        return $this->deliveryMethod->price() + parent::getTotalCost();
    }

    public function distributer(): Distributer
    {
        return $this->distributer;
    }


}