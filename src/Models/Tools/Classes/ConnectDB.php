<?php

namespace App\Models\Tools\Classes;

class ConnectDB
{
    private static $instancePDO;

    public static function getInstanceToPDO()
    {
        if(static::$instancePDO!==null)
        {
            return static::$instancePDO;
        }
        self::$instancePDO = new \PDO("mysql:host=".\NOM_HOTE.";dbname=".\DB_NAME,\DB_USER,\DB_PASSWORD,array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));

        return static::$instancePDO;
    }
}





