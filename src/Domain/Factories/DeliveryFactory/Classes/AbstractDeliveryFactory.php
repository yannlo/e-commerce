<?php


namespace App\Domain\Factory\DeliveryFactory\Classes;

use App\Domain\Address\Address;
use App\Domain\Delivery\Interfaces\DeliveryInterface;
use App\Domain\Factory\DeliveryFactory\Interfaces\DeliveryFactoryInterface;

abstract class AbstractDeliveryFactory implements DeliveryFactoryInterface
{
    protected Address $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    abstract public function createDelivery(): DeliveryInterface;

    final public function price(): int
    {
        $delivery = $this -> createDelivery();
        return $delivery->getPrice();
    }
}