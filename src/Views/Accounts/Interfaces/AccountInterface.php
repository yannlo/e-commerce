<?php

namespace App\Views\Accounts\Interfaces;

interface AccountInterface 
{
    public static function login(array $data=[]): void;

    public static function signup(array $data=[]): void;

    public static function account(array $data=[]): void;
}