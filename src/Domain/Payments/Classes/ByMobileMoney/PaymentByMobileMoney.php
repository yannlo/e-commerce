<?php

namespace App\Domain\Payment\Classes\ByMobileMoney;

use App\Domain\Payment\Interfaces\PaymentInterface;

abstract class PaymentByMobileMoney implements PaymentInterface
{
    protected $mobileNumber;
    
    abstract public function payOrder(?int $price): mixed;
}