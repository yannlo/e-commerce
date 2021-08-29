<?php


namespace App\Domain\Accounts\Classes;

use App\Domain\Accounts\Classes\Exceptions\AccountException;

Abstract Class Account
{
    use \App\Domain\Tools\Hydration;
    
    protected int $id=0;
    protected string $email='';
    protected string $password='';

    // GETTERS
    
    public function id(): int
    {
        return $this->id;
    }
    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
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
}