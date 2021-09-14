<?php

namespace App\Controllers\Accounts\Interfaces;

interface AccountControllerInterface
{
    public static function login(): void;

    public static function logout(): void;

    public static function signup(): void;

    public static function account(?string $section): void;
}