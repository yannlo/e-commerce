<?php

namespace App\Domain\Payment\Classes\ByMobileMoney;

use App\Domain\Payment\Interfaces\PaymentInterface;

class PaymentByOrangeMoney implements PaymentInterface
{
    public function payOrder(?int $price): bool
    {
        return true;
    }
}