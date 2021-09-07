<?php

namespace App\Domain\Delivery\Classes;

class HomeDelivery extends AbstractDelivery
{
    private int $price=1000;

    public function getPrice(): int
    {
        return $this->price;
    }

}