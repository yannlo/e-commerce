<?php


namespace App\Domain\Accounts\Classes;

use App\Domain\Accounts\Classes\Exceptions\CustomerException;

class Customer extends Account
{
    private string $firstName='';
    private string $lastName='';
    private string $birthDate='';
    private string $phoneNumber ='';

    // constructor
    public function __construct(array $data){
        $this->hydrate($data);
    }

    //GETTERS
    public function firstName(): string
    {
        return (string) $this->firstName;
    }

    public function phoneNumber(): string
    {
        return (string) $this->phoneNumber;
    }

    public function birthDate(): string
    {
        return (string) $this->birthDate;
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
            throw new CustomerException("Invalid size to first Name",100);
            return;
        }

        $this-> firstName = $firstName;
    }

    public function setLastName($lastName): void
    {
        $lastName= (string) $lastName;
        
        if(strlen($lastName)<3)
        {
            throw new CustomerException("Invalid size to last Name",100);
            return;
        }

        $this-> lastName = $lastName;
    }


    public function setPhoneNumber($phoneNumber): void
    {
        $phoneNumber = (string) $phoneNumber;

        $phoneNumber = (string) preg_replace(' ', '', $phoneNumber);

        if(strlen($phoneNumber)!==10)
        {
            throw new CustomerException("Invalid number phone",100);
            return;
        }

        $this-> phoneNumber = $phoneNumber;
    }
    

    public function setBirthDate(string $birthDate):void
    {
        
    }

    public function __isset($name): bool
    {
        if(empty($this->$name) || isset($this->$name)){
            return false;
        }
        return true;
    }

}