<?php

namespace App\Domain\Factory\PaymentFactory\Classes;

use App\Domain\Payment\Classes\PaymentByCard;
use App\Domain\Payment\Interfaces\PaymentInterface;

class PaymentByCardFactory extends AbstractPaymentFactory
{
    public function createPayment(): PaymentInterface
    {
        return new PaymentByCard;
    }
}