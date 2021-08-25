<?php

namespace App\Models\Orders\Interfaces;

use App\Domain\Orders\Order;
use App\Models\Tools\Interfaces\DataSaver;

interface OrderDataSaver extends DataSaver
{
    public function add(Order $order): void;

    public function update(Order $order): void;

    public function delete(?Order $order): void;
}
