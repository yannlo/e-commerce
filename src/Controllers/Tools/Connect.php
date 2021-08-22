<?php

namespace App\Controllers\Tools;

trait Connect
{
    public static function is_connected(string $name) : mixed
    {
        if(isset($_SESSION[$name]))
        {
            return $_SESSION[$name];
        }
        
        return false;
    }

}