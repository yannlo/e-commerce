<?php

namespace App\Domain\Factory\PaymentFactory\Classes;

use App\Domain\Payment\Classes\PaymentByMTNMoney;
use App\Domain\Payment\Interfaces\PaymentInterface;

class PaymentByMTNMoneyFactory extends AbstractPaymentFactory
{
    public function createPayment(): PaymentInterface
    {
        return new PaymentByMTNMoney;
    }
}