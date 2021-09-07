<?php


namespace App\Domain\Factory\DeliveryFactory\Classes;

use App\Domain\Delivery\Classes\HomeDelivery;
use App\Domain\Delivery\Interfaces\DeliveryInterface;

class HomeDeliveryFactory extends AbstractDeliveryFactory
{
    public function createDelivery(): DeliveryInterface
    {
        return new HomeDelivery($this->address);
    }
}