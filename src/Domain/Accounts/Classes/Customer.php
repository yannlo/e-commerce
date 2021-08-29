<?php


namespace App\Domain\Accounts\Classes;

use App\Domain\Accounts\Classes\Exceptions\CustomerException;

class Customer extends Account
{
    private string $firstName='';
    private string $lastName='';

    // constructor
    public function __construct(array $data){
        $this->hydrate($data);
    }

    //GETTERS
    public function firstName(): string
    {
        return (string) $this->firstName;
    }

    public function lastName(): string
    {
        return (string) $this->lastName;
    }

    //SETTERS
    public function setFirstName($firstName): void
    {
        $firstName= (string) $firstName;

        if(strlen($firstName)< 3)
        {
            throw new CustomerException("le nom est trop court");
            return;
        }

        $this-> firstName = $firstName;
    }

    public function setLastName($lastName): void
    {
        $lastName= (string) $lastName;
        
        if(strlen($lastName)<3)
        {
            throw new CustomerException("le prenom est trop court");
            return;
        }

        $this-> lastName = $lastName;
    }

    public function __isset($name): bool
    {
        if(empty($this->$name) || isset($this->$name)){
            return false;
        }
        return true;
    }

}