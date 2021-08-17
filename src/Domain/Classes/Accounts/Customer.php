<?php
declare(strict_types=1);

namespace App\Domain\Classes\Accounts;
use App\Domain\Classes\Account\Exceptions\CustomerException;

class Customer extends DefaultAccount
{
    private $firstName;
    private $lastName;


    // constructor
    public function __construct(array $data){
        $this->hydrate($data);
    }

    //GETTERS
    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }



    // SETTERS

    public function setFirstName($firstName): void
    {
        $firstName= (string) $firstName;

        if(strlen($firstName)<3)
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
}