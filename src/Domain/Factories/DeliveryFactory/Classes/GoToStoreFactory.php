<?php


namespace App\Domain\Factory\DeliveryFactory\Classes;

use App\Domain\Delivery\Classes\GoToStore;
use App\Domain\Delivery\Interfaces\DeliveryInterface;

class GoToStoreFactory extends AbstractDeliveryFactory
{
    public function createDelivery(): DeliveryInterface
    {
        return new GoToStore($this->address);
    }
}