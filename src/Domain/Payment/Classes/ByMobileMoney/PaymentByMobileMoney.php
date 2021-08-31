<?php

namespace App\Domain\Payment\Classes\ByMobileMoney;

use App\Domain\Payment\Interfaces\PaymentInterface;

class PaymentByMobileMoney implements PaymentInterface
{
    protected $mobileNumber;
    
    public function payOrder(?int $price): bool
    {
        return true;
    }
}