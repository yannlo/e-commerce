<?php

namespace App\Domain\Factory\PaymentFactory\Classes;

use App\Domain\Payment\Interfaces\PaymentInterface;
use App\Domain\PaymentFactory\Interfaces\PaymentFactoryInterface;

abstract class AbstractPaymentFactory implements PaymentFactoryInterface
{
    abstract public function createPayment(): PaymentInterface;

    final public function pay(?int $price): bool
    {
        $payment = $this->createPayment();
        return $payment->payOrder($price);
    }
}