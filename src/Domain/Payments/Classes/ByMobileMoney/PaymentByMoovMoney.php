<?php

namespace App\Domain\Payment\Classes\ByMobileMoney;

use App\Domain\Payment\Interfaces\PaymentInterface;

class PaymentByMoovMoney implements PaymentInterface
{
    public function payOrder(?int $price): mixed
    {
        return true;
    }
}