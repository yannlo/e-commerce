<?php

namespace App\Domain\Delivery\Classes;

use App\Domain\Address\Address;
use App\Models\Tools\Classes\ConnectDB;
use App\Domain\Orders\OrderByDistributor;
use App\Models\Address\CommonListManager;
use App\Domain\Delivery\Interfaces\DeliveryInterface;
use App\Domain\Delivery\Classes\Exceptions\DeliveryException;

Abstract class AbstractDelivery implements DeliveryInterface
{
    use \App\Domain\Tools\Hydration;

    protected Address $address;
    protected OrderByDistributor $orderByDistributor;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }
    
    // GETTERS
    public function address():Address
    {
        return $this->address;
    }

    public function OrderByDistributor():OrderByDistributor
    {
        return $this->orderByDistributor;
    }


    // SETTERS
    public function setAddress($address):void
    {
        if(!is_a($address,get_class(new Address([]))))
        {
            throw new DeliveryException('Argument is not a address',301);
            return;
        }

        if($address->city() !== CITY)
        {
            throw new DeliveryException('city in Address is not valid',101);
            return;
        }

        $commonList = (new CommonListManager(ConnectDB::getInstanceToPDO()))->get() ;

        if(!in_array($address->common(),$commonList))
        {
            throw new DeliveryException('common in Address is not valid',101);
            return;
        }

        $this->address = $address;
    }

    public function setOrderByDistributor($orderByDistributor): void
    {
        if(!is_a($orderByDistributor,get_class(new OrderByDistributor([]))))
        {
            throw new DeliveryException('Argument is not a OrderByDistributor ',301);
            return;
        }

        $this->orderByDistributor = $orderByDistributor;
    }
    
}