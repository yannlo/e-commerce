<?php


namespace App\Domain\Accounts\Classes;

use App\Domain\Address\Address;
use App\Domain\Pictures\Picture;
use App\Models\Tools\Classes\ConnectDB;
use App\Models\Address\CommonListManager;
use App\Domain\Accounts\Classes\Exceptions\AccountException;

Abstract Class Account
{
    use \App\Domain\Tools\Hydration;
    
    protected int $id=0;
    protected Picture $picture;
    protected string $email='';
    protected string $password='';
    protected Address $address;

    // GETTERS
    
    public function id(): int
    {
        return $this->id;
    }

    public function picture(): Picture
    {
        return $this->picture;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function address():Address
    {
        return $this->address;
    }

    // SETTERS
    public function setId($id): void
    {
        $id = (int)$id;


        if($id <=0)
        {
            throw new AccountException("l'id n'est pas strictement positive");
            return; 
        }

        $this-> id = $id;
    }

    public function setPicture($picture):void
    {
        // code
    }

    public function setEmail($email): void
    {
        $email = (string) $email;
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            throw new AccountException("l'email est invalide");
            return;
        }

        $this -> email = $email;
    }

    public function setPassword($password):void
    {

        $password= (string) $password;

        if(strlen($password)> 255)
        {
            throw new AccountException("probleme au niveau du mot de passe");
            return;
        }

        $this -> password = $password;
    }

    public function setAddress($address):void
    {
        if(!is_a($address,get_class(new Address([]))))
        {
            throw new AccountException('Argument is not a address',301);
            return;
        }
        if($address->city() !== CITY)
        {
            throw new AccountException('city in Address is not valid',101);
            return;
        }

        $commonList = (new CommonListManager(ConnectDB::getInstanceToPDO()))->get() ;

        if(!in_array($address->common(),$commonList))
        {
            throw new AccountException('common in Address is not valid',101);
            return;
        }
        $this->address= $address;

    }
}