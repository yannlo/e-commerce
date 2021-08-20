<?php

namespace App\Controllers\Account\Classes;


use App\Views\Generals\ErrorViews;
use App\Models\Account\DistributerModels;
use  App\Domain\Classes\Accounts\DefaultAccount;
use  App\Views\Account\Classes\DistributerViews;
use App\Controllers\Account\Interfaces\accountInterface;

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
