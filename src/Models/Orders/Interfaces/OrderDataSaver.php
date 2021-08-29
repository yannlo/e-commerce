<?php

namespace App\Models\Orders\Interfaces;

use App\Domain\Orders\Order;

interface OrderDataSaver
{
    public function add(Order $order): void;

    public function update(Order $order): void;

    public function delete(?Order $order): void;
}
