<?php


namespace App\Domain\Factory\PaymentFactory\Interfaces;

use App\Domain\Payment\Interfaces\PaymentInterface;

interface PaymentFactoryInterface
{
    public function createPayment(): PaymentInterface;

    public function pay(?int $price): bool;
}