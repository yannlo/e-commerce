<?php

namespace App\Models\Orders\Interfaces;

use App\Domain\Orders\Cart;
use App\Domain\Accounts\Classes\Customer;

interface CartCRUD
{
    public function get(?Customer $customer): ?Cart;
    
    public function add(Cart $order): void;

    public function update(Cart $order): void;

    public function delete(?Cart $order): void;

}
