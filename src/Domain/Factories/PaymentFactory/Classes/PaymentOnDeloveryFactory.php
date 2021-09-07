<?php

namespace App\Domain\Factory\PaymentFactory\Classes;

use App\Domain\Payment\Classes\PaymentOnDelovery;
use App\Domain\Payment\Interfaces\PaymentInterface;

class PaymentOnDeloveryFactory extends AbstractPaymentFactory
{
    public function createPayment(): PaymentInterface
    {
        return new PaymentOnDelovery;
    }
}