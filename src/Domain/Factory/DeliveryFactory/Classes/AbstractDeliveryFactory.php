<?php


namespace App\Domain\Factory\DeliveryFactory\Classes;

use App\Domain\Delivery\Interfaces\DeliveryInterface;
use App\Domain\Factory\DeliveryFactory\Interfaces\DeliveryFactoryInterface;

abstract class AbstractDeliveryFactory implements DeliveryFactoryInterface
{
    abstract public function createDelivery(): DeliveryInterface;

    final public function price(): int
    {
        $delivery = $this -> createDelivery();
        return $delivery->getPrice();
    }
}