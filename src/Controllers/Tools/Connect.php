<?php

namespace App\Controllers\Tools;
use App\Models\Tools\Classes\ConnectDB;
use App\Models\Accounts\CustomerManager;
use App\Domain\Accounts\Classes\Customer;
use App\Models\Accounts\DistributorManager;
use App\Domain\Accounts\Classes\Distributor;
use App\Controllers\Tools\Exceptions\ConnectException;

Class Connect
{
    private static array $userType = array(
        "customer",
        "distributor"
    );

    private static CustomerManager $customerManager;
    private static DistributorManager $distributorManager;

    public static function userConnection(string $type, int $id)
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
        $data['id'] = $id;
        $_SESSION['account']=$data;
    }

    public static function getUser(): null|Customer|Distributor
    {
        // if(!self::userConnectionVerify())
        // {
        //     throw new ConnectException('user is not connected',00);
        //     return null;
        // }

        // $data = $_SESSION['account'];

        $data = [
            "id"=>6,
            "type"=>"customer"
        ];
        switch($data['type'])
        {
            case 'customer':
                self::$customerManager= new CustomerManager(ConnectDB::getInstanceToPDO());
                return self::$customerManager->getOnce($data['id']);

            case 'distributor':
                self::$distributorManager= new DistributorManager(ConnectDB::getInstanceToPDO());
                return self::$distributorManager->getOnce($data['id']);
        }
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