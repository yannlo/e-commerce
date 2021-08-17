<?php

namespace App\Controllers\Account\Classes;


use App\Models\Account\CustomerModels;
use  App\Views\Account\Classes\CustomerViews;
use App\Domain\Classes\Accounts\DefaultAccount;
use App\Controllers\Account\Interfaces\accountInterface;

class CustomerController implements accountInterface
{
    public static function  index(): void
    {
        CustomerViews::index();
    }

    public static function login(): void
    {
        CustomerViews::login();
    }

    public static function signup(): void
    {
        CustomerViews::signup();
    }

    public static function account(): void
    {
        CustomerViews::account();
    }

}