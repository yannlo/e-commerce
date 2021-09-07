<?php


namespace App\Domain\Factory\DeliveryFactory\Classes;

use App\Domain\Delivery\Classes\MeetingPointDelivery;
use App\Domain\Delivery\Interfaces\DeliveryInterface;

class MeetingPointDeliveryFactory extends AbstractDeliveryFactory
{
    public function createDelivery(): DeliveryInterface
    {
        return new MeetingPointDelivery($this->address);
    }
}