<?php

namespace App\Views\Account\Interfaces;

use \App\Views\Generals\GeneralViewsInterface;


interface AccountInterface extends GeneralViewsInterface
{
    public static function login(array $data=[]): void;

    public static function signup(array $data=[]): void;

    public static function account(array $data=[]): void;
}