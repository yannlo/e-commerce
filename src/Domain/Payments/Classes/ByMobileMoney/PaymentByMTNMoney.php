<?php

namespace App\Domain\Payment\Classes\ByMobileMoney;


class PaymentByMTNMoney extends PaymentByMobileMoney
{
    public function payOrder(?int $price): mixed
    {
        return true;
    }
}