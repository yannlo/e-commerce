<?php

namespace App\Domain\Orders;

use App\Domain\Accounts\Classes\Customer;
use App\Domain\Orders\Exceptions\OrderException;

Abstract class AbstractOrder
{
    use \App\Domain\Tools\Hydration;

    protected int $id=0;
    protected Customer $customer;

    public function __construct(array $data)
    {
        $this-> hydrate($data);
    }

    // GETTERS
    public function id():int
    {
        return $this->id;
    }
    
    public function  customer(): Customer
    {
        return $this->customer;
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

}