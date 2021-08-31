<?php

namespace App\Domain\Factory\PaymentFactory\Classes;

use App\Domain\Payment\Classes\PaymentByPayPal;
use App\Domain\Payment\Interfaces\PaymentInterface;

class PaymentByPayPalFactory extends AbstractPaymentFactory
{
    public function createPayment(): PaymentInterface
    {
        return new PaymentByPayPal;
    }
}