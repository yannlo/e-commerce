<?php

namespace App\Domain\Delivery\Classes;

use App\Domain\Delivery\Interfaces\DeliveryInterface;

class MeetingPointDelivery implements DeliveryInterface
{
    public function getPrice(): int
    {
        $price = 0;
        return $price;
    }
}