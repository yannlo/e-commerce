<?php

namespace App\Domain\Factory\DeliveryFactory\Interfaces;

use App\Domain\Delivery\Interfaces\DeliveryInterface;

interface DeliveryFactoryInterface
{
    public function createDelivery(): DeliveryInterface;

    public function price(): int;
}