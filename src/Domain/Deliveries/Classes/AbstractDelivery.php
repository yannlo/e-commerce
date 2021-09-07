<?php

namespace App\Domain\Delivery\Classes;

use App\Domain\Address\Address;
use App\Models\Tools\Classes\ConnectDB;
use App\Models\Address\CommonListManager;
use App\Domain\Delivery\Classes\Exceptions\DeliveryException;
use App\Domain\Delivery\Interfaces\DeliveryInterface;

Abstract class AbstractDelivery implements DeliveryInterface
{
    protected Address $address;

    public function __construct(Address $address)
    {
        $this->setAddress($address);
    }
    
    public function address():Address
    {
        return $this->address;
    }

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
}