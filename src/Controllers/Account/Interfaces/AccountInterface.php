<?php

namespace App\Controllers\Account\Interfaces;

use App\Domain\Classes\Accounts\DefaultAccount;
use App\Controllers\Generals\GeneralControllerInterface;

interface AccountInterface extends GeneralControllerInterface
{
    public static function login(): void;

    public static function signup(): void;

    public static function account(): void;
}