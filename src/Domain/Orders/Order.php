<?php

namespace App\Domain\Orders;

use App\Domain\Accounts\Classes\Customer;
use App\Domain\Orders\Exceptions\OrderException;

class Order
{
    use \App\Domain\Tools\Hydration;

    private int $id=0;
    private array $orderLines=[];
    private Customer $customer;
    private int $status = self::CART;

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
            throw new OrderException('Invalid id to order');
            return;
        }

        $this->id = $id;
    }

    public function setOrderLines($orderLines): void
    {
        $orderLines = (array) $orderLines;

        // if (empty($orderLines))
        // {
        //     throw new OrderException('OrderLines is empty');
        //     return;
        // }

        foreach ($orderLines as $orderLine)
        {
            if(!is_a($orderLine,get_class(new OrderLine([]))))
            {
                throw new OrderException('element in OrderLines is not to OrderLine class to the index: '.array_search($orderLine,$orderLines));
                return;
            }
        }

        $this->orderLines = $orderLines;
    }

    public function setCustomer($customer): void
    {

        if( !is_a( $customer, get_class(new Customer([])) ) ){
            throw new OrderException ("is not element to Customer class");
            return;
        }

        // if($customer->id() === null || $customer->id() <= 0 )
        // {
        //     throw new OrderException ("Invalid customer");
        //     return;
        // }

        $this->customer = $customer;
    }

    public function setStatus($status): void
    {
        if(!in_array( $status, [self::CART,self::BEING_DELIVERED, self::BEING_PROCESSED, self::FINISH] ))
        {
            throw new OrderException('Invalid status');
            return;
        }

        $this->status = $status;

    }

    // METHODS

    // to modified OrderLines array
    public function addOrderLine(OrderLine $orderLine): void
    {
        $this->orderLines[] = $orderLine;
    }

    public function setOrderLine(OrderLine $orderLine): void
    {
        $key = $this->OrderLineExist($orderLine);

        $this->orderLines[$key] = $orderLine;
    }

    public function unsetOrderLine(OrderLine $orderLine, bool $confirmation=false): void
    {
        $key=$this->foundOrderLine($orderLine);

        if(!$confirmation)
        {
            throw new OrderException('unset orderLine is not confirmed');
            return;
        }

        $key = $this->OrderLineExist($orderLine);

        unset($this->orderLines[$key]);
    }

    // to modified OrderLine in array
    private function foundOrderLine(OrderLine $orderLine): int|bool
    {
        foreach($this->orderLines as $orderLineToUpdate)
        {
            if($orderLineToUpdate->id()=== $orderLine->id())
            {
                return key($orderLineToUpdate);
            }
        }

        return false;
    }

    // order line exist
    public function OrderLineExist($orderLine)
    {
        $key=$this->foundOrderLine($orderLine);

        if($key===false)
        {
            throw new OrderException('Orderline not exist in order');
            return;
        }

        return $key;
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

    public function jsonEncoder(): string
    {
        $data = array(
            "id"=>$this->id,
            "status"=>$this->status()
        );
        if($this->customer->id() !== 0)
        {
            $data["customer"]=$this->customer->jsonEncoder();
        }
        $orderLinesJson=[];
        foreach($this->orderLines  as $orderLine)
        {
            $orderLinesJson[]=$orderLine->jsonEncoder();
        }
        $data['orderLines']=$orderLinesJson;
        return json_encode($data,JSON_FORCE_OBJECT);
    }

    public static function jsonDecoder(string $json)
    {
        $data = (array) json_decode($json);  
        if(isset($data['customer']))
        {
            $data['customer'] = Customer::jsonDecoder($data['customer']);
        }
        $orderLines =[];

        foreach($data['orderLines'] as $orderLine)
        {
            $orderLines[] = OrderLine::jsonDecoder($orderLine);
        }

        $data['orderLines'] = $orderLines;

        return new self($data);
    }
}