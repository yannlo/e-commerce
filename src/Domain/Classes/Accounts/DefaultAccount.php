<?php
declare(strict_types=1);

namespace App\Domain\Classes\Accounts;
use App\Domain\Classes\Account\Exceptions\AccountException;

abstract class DefaultAccount
{

    use \App\Domain\Traits\Hydration;



    protected int $id=0;
    protected string $email="";
    protected string $password="";

    
    public function __construct($data){
        $this->hydrate($data);
    }
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

    public function setPassword($password){
        if(strlen($password)> 255)
        {
            throw new AccountException("probleme au niveau du mot de passe");
            return;
        }

        $this -> password = $password;
    }
}