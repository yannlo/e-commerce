<?php


namespace App\Domain\Orders;

use App\Domain\Orders\OrderLine;
use App\Domain\Orders\Exceptions\CartException;

/**
 * Cart
 * 
 * use to represent a cart
 * 
 */
class Cart extends AbstractOrder
{
    protected array $orderLines=[];

    // GETTERS    
    /**
     * orderLines
     * 
     * return orderline array 
     *
     * @return array
     */
    public function orderLines(): array
    {
        return $this->orderLines;
    }


    // SETTERS    
    /**
     * setOrderLines
     * 
     * use to modifier orderLine array
     *
     * @param  mixed $orderLines
     * @return void
     */
    public function setOrderLines(array $orderLines): void
    {
        $orderLines = (array) $orderLines;

        if (empty($orderLines))
        {
            throw new CartException('OrderLines is empty',200);
            return;
        }

        foreach ($orderLines as $orderLine)
        {
            if(!is_a($orderLine,get_class(new OrderLine([]))))
            {
                throw new CartException('element in OrderLines is not to OrderLine class to the index: '.array_search($orderLine,$orderLines),302);
                return;
            }

            $orderLine->setCart($this);
        }

        $this->orderLines = $orderLines;
    }


    // Method to modifier orderLine
    // to modified OrderLines array
  
    /**
     * addOrderLine
     *
     *  use to add orderLine in OrderLines
     *
     * @param  OrderLine $orderLine
     * @return void
     */
    public function addOrderLine(OrderLine $orderLine): void
    {
        if($this->orderLineExist($orderLine))
        {
            throw new CartException('Orderline exist',602);
            return;
        }

        $this->orderLines[] = $orderLine;
    }
    
    
    /**
     * updateOrderLine
     * 
     * use to modifier orderLine attributs value to specific orderline
     *
     * @param  OrderLine $orderLine
     * @return void
     */
    public function updateOrderLine(OrderLine $orderLine): void
    {
        if(!$this->orderLineExist($orderLine))
        {
            throw new CartException('Orderline not exist',602);
            return;
        }
        
        
        $orderLineFound = $this->foundOrderLine($orderLine);


        if($orderLineFound->id() !== $orderLine->id())
        {
            $orderLineFound->setId($orderLine->id());
        }

        if($orderLineFound->quantity() !== $orderLine->quantity())
        {
            $orderLineFound->setQuantity($orderLine->quantity());
        }

    }
    

    /**
     * deleteOrderLine
     *
     * use to delete orderLine in orderLines
     * 
     * @param  OrderLine $orderLine
     * @param  bool $confirmation false by default
     * @return void
     */
    public function deleteOrderLine(OrderLine $orderLine, bool $confirmation=false): void
    {
        
        if(!$confirmation)
        {
            throw new CartException('unset orderLine is not confirmed',402);
            return;
        }

        if(!$this->orderLineExist($orderLine))
        {
            throw new CartException('Orderline not exist',602);
            return;
        }

        foreach($this->orderLines as $key => $orderLineToUpdate)
        {
            if($orderLineToUpdate->item()->id() === $orderLine->item()->id())
            {
                unset($this->orderLines[$key]);
                break;
            }
        }

    }


    // verified orderLine in array 
    
    /**
     * foundOrderLine
     * 
     * use to found orderLine in orderLines
     *
     * @param  mixed $orderLine
     * @return OrderLine
     * return orderLine if it founds this
     * 
     * @return false
     * return false if it not found orderLine in orderLines
     */
    public function foundOrderLine(OrderLine $orderLine): OrderLine|false
    {
        
        foreach($this->orderLines() as $key => $orderLineToUpdate)
        {
            if($orderLineToUpdate->item()->id() === $orderLine->item()->id())
            {
                return $this->orderLines[$key];
            }
        }
        return false;
    }

      
    /**
     * orderLineExist
     *
     * 
     * @param  mixed $orderLine
     * @return bool
     * true if the orderLine in parameter exist
     */
    public function orderLineExist($orderLine): bool
    {
        $orderLine = $this->foundOrderLine($orderLine);   

        if($orderLine === false)
        {
            return false;
        }
        return true;
    }

    // Get cost method    
    /**
     * getTotalCost
     * 
     * returns the total of the addition of the orderLine
     *
     * @return int
     */
    public function getTotalCost(): int
    {
        $totalCost= 0;
        foreach($this->orderLines  as $orderLine){
            $totalCost += $orderLine -> getCost();
        }

        return $totalCost;
    }
}