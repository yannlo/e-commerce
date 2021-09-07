<?php

namespace App\Domain\Factory\PaymentFactory\Classes;

use App\Domain\Payment\Classes\PaymentByOrangeMoney;
use App\Domain\Payment\Interfaces\PaymentInterface;

class PaymentByOrangeMoneyFactory extends AbstractPaymentFactory
{
    public function createPayment(): PaymentInterface
    {
        return new PaymentByOrangeMoney;
    }
}