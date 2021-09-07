<?php


namespace App\Domain\Accounts\Classes;

use App\Domain\Accounts\Classes\Exceptions\CustomerException;

class Customer extends Account
{
    private string $firstName='';
    private string $lastName='';
    private string $birthDate='';
    private int $sex=self::UNKNOWN;
    
    // CONSTANTS
    const UNKNOWN =0;
    const MAN = 1;
    const WOMAN = 2;

    // constructor
    public function __construct(array $data){
        $this->hydrate($data);
    }
    
    //GETTERS
    public function firstName(): string
    {
        return (string) $this->firstName;
    }

    public function birthDate(): string
    {
        return (string) $this->birthDate;
    }

    public function lastName(): string
    {
        return (string) $this->lastName;
    }

    public function sex(): int
    {
        return $this->sex;
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

    

    public function setBirthDate(string $birthDate):void
    {
        // code
    }

    public function __isset($name): bool
    {
        if(empty($this->$name) || isset($this->$name)){
            return false;
        }
        return true;
    }

    public function setSex(int $sex):void
    {
        if(!in_array($sex,[self::MAN,self::WOMAN, self::UNKNOWN]))
        {
            throw new CustomerException("Invalid parameter value for sex");
            return;
        }

        $this->$sex = $sex;
    }
}