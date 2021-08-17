<?php

namespace App\Controllers\Account\Classes;


use App\Models\Account\DistributerModels;
use  App\Views\Account\Classes\DistributerViews;
use App\Controllers\Account\Interfaces\accountInterface;
use  App\Domain\Classes\Accounts\DefaultAccount;

class DistributerController implements AccountInterface
{

    public static function  index(): void
    {
        DistributerViews::index();
    }

    public static function login(): void
    {
        DistributerViews::login();
    }

    public static function signup(): void
    {
        DistributerViews::signup();
    }

    public static function account(): void
    {
        DistributerViews::account();
    }

}
