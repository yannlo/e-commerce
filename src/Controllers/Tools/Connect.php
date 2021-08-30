<?php

namespace App\Controllers\Tools;
use App\Controllers\Tools\Exceptions\ConnectException;

Class Connect
{
    private static array $userType = array(
        "customer",
        "distributer"
    );


    public static function userConnection(string $type,array $data)
    {
        if(self::userConnectionVerify($type))
        {
            return;
        }

        if(!in_array($type,self::$userType))
        {
            throw new ConnectException('Invalid user type',101);
            return;
        }

        $data['type'] = $type;
        $_SESSION['account']=$data;
    }

    public static function getUser()
    {
        if(!self::userConnectionVerify())
        {
            throw new ConnectException('user is not connected',00);
            return;
        }

        $data = $_SESSION['account'];
        unset($data['type']);
        return $data;
    }

    public static function userConnectionVerify()
    {
        if(isset($_SESSION['account']))
        {
            return true;
        }

        return false;
    }

    public static function typeConnectionVerify(string $accountType) : mixed
    {
        if(!self::userConnectionVerify())
        {
            return false;
        }

        if($_SESSION['account']['type'] != $accountType)
        {
            return false;
        }

        return true;
    }


}