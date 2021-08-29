<?php

namespace App\Controllers\Accounts\Interfaces;

use App\Controllers\Generals\GeneralControllerInterface;

interface AccountControllerInterface extends GeneralControllerInterface
{
    public static function login(): void;

    public static function logout(): void;

    public static function signup(): void;

    public static function account(): void;
}