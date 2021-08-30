<?php

namespace App\Domain\Tools;

class ErrorExplode
{
    public static function explodeCode($errorCode): array
    {
        $errorCode = preg_replace("/^([0-9]{1})([0-9]{2})$/","$1 $2",(string)$errorCode);
        $errorCode = explode(" ",$errorCode);
        return $errorCode; 
    }
}