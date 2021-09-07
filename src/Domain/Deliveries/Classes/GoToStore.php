<?php

namespace App\Domain\Delivery\Classes;

use App\Domain\Delivery\Interfaces\DeliveryInterface;

class GoToStore extends AbstractDelivery
{
    public function getPrice(): int
    {
        return 0;
    }
}