<?php

namespace App\Domain\Payment\Interfaces;

interface PaymentInterface
{
    public function payOrder(?int $price):bool;
}