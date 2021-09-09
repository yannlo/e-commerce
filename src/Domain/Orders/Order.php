<?php

namespace App\Domain\Orders;

use App\Domain\Accounts\Classes\Customer;
use App\Domain\Orders\Exceptions\OrderException;

class Order
{
    use \App\Domain\Tools\Hydration;

    protected int $id=0;
    protected array $orderLines=[];
    protected Customer $customer;
    protected int $status = self::CART;

    // CONSTANTS
    const CART = 0;
    const BEING_PROCESSED = 1;
    const BEING_DELIVERED = 2;
    const FINISH = 3;

    public function __construct(array $data)
    {
        $this->orderLines = array();
        $this->customer= new Customer([]);
        $this-> hydrate($data);
    }

    // GETTERS
    public function id():int
    {
        return $this->id;
    }

    public function orderLines(): array
    {
        return $this->orderLines;
    }
    
    public function  customer(): Customer
    {
        return $this->customer;
    }

    public function status(): int
    {
        return $this->status;
    }


    // SETTERS
    public function setId($id): void
    {
        $id = (int) $id;

        if ($id < 0) 
        {
            throw new OrderException('Invalid id to order',100);
            return;
        }

        $this->id = $id;
    }

    public function setOrderLines(array $orderLines): void
    {
        $orderLines = (array) $orderLines;

        if (empty($orderLines))
        {
            throw new OrderException('OrderLines is empty',200);
            return;
        }

        foreach ($orderLines as $orderLine)
        {
            if(!is_a($orderLine,get_class(new OrderLine([]))))
            {
                throw new OrderException('element in OrderLines is not to OrderLine class to the index: '.array_search($orderLine,$orderLines,),302);
                return;
            }
        }

        $this->orderLines = $orderLines;
    }

    public function setCustomer($customer): void
    {

        if( !is_a( $customer, get_class(new Customer([])) ) ){
            throw new OrderException ("is not element to Customer class",301);
            return;
        }

        if($customer->id() === null || $customer->id() < 0 )
        {
            throw new OrderException ("Invalid customer",101);
            return;
        }

        $this->customer = $customer;
    }

    public function setStatus($status): void
    {
        if(!in_array( $status, [self::CART,self::BEING_DELIVERED, self::BEING_PROCESSED, self::FINISH] ))
        {
            throw new OrderException('Invalid status',100);
            return;
        }

        $this->status = $status;

    }

    // METHODS

    // to modified OrderLines array
    public function addOrderLine(OrderLine $orderLine): void
    {
        if($this->orderLineExist($orderLine))
        {
            throw new OrderException('Orderline exist',602);
            return;
        }

        $this->orderLines[] = $orderLine;
    }

    public function setOrderLine(OrderLine $orderLine): void
    {
        
        if(!$this->orderLineExist($orderLine))
        {
            throw new OrderException('Orderline not exist',602);
            return;
        }
        
        $key = $this->foundOrderLineKey($orderLine);
        
        $this->orderLines[$key] = $orderLine;
    }

    public function unsetOrderLine(OrderLine $orderLine, bool $confirmation=false): void
    {
        
        if(!$confirmation)
        {
            throw new OrderException('unset orderLine is not confirmed',402);
            return;
        }

        if(!$this->orderLineExist($orderLine))
        {
            throw new OrderException('Orderline not exist',602);
            return;
        }
        

        $key = $this->foundOrderLineKey($orderLine);


        unset($this->orderLines[$key]);
    }

    // to modified OrderLine in array
    public function foundOrderLineKey(OrderLine $orderLine): int|bool
    {
        foreach($this->orderLines as $key => $orderLineToUpdate)
        {
            if($orderLineToUpdate->item()->id() === $orderLine->item()->id())
            {
                return $key;
            }
        }

        return false;
    }

    // order line exist       
    /**
     * orderLineExist
     *
     * 
     * @param  mixed $orderLine
     * @return bool
     * true if the orderlin in parameter exist
     */
    public function orderLineExist($orderLine): bool
    {
        $key=$this->foundOrderLineKey($orderLine);

        if($key===false)
        {
            return false;
        }

        return true;
    }

    // get cost total
    public function getTotalCost(): int
    {
        $totalCost= 0;
        foreach($this->orderLines  as $orderLine){
            $totalCost += $orderLine -> getCost();
        }

        return $totalCost;
    }
}