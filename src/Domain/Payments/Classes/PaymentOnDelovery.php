<?php

namespace App\Domain\Payment\Classes;

use App\Domain\Payment\Interfaces\PaymentInterface;

class PaymentOnDelovery implements PaymentInterface
{
    public function payOrder(?int $price): mixed
    {
        return true;
    }
}