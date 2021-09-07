<?php

namespace App\Domain\Address;

use App\Domain\Address\Exceptions\AddressException;

class Address
{
    use \App\Domain\Tools\Hydration;

    private string $country;
    private string $city;
    private string $common;
    private string $district;
    private string $phoneNumber;


    public function __construct (array $data)
    {
        $this->hydrate($data);
    }


    // GETTERS
    public function country()
    {
        return $this->country;
    }

    public function city()
    {
        return $this->city;
    }

    public function common()
    {
        return $this->common;
    }

    public function district()
    {
        return $this->district;
    }
    public function phoneNumber(): string
    {
        return (string) $this->phoneNumber;
    }



    // SETTERS
    public function setCountry($country) :void
    {
        $country = (string) $country;

        if (strlen($country) <3)
        {
            throw new AddressException('Invalid country',100);
            return;
        }

        $this->country = $country;
    }

    public function setCity($city) :void
    {
        $city = (string) $city;

        if (strlen($city) <3)
        {
            throw new AddressException('Invalid city',100);
            return;
        }

        $this->city = $city;
    }

    public function setCommon($common) :void
    {
        $common = (string) $common;

        if (strlen($common) <3)
        {
            throw new AddressException('Invalid common',100);
            return;
        }

        $this->common = $common;
    }

    public function setDistrict($district) :void
    {
        $district = (string) $district;

        if (strlen($district) <3)
        {
            throw new AddressException('Invalid district',100);
            return;
        }

        $this->district = $district;
    }

    public function setPhoneNumber($phoneNumber): void
    {
        $phoneNumber = (string) $phoneNumber;

        $phoneNumber = (string) preg_replace(' ', '', $phoneNumber);

        if(strlen($phoneNumber)!==10)
        {
            throw new AddressException("Invalid number phone",100);
            return;
        }

        $this-> phoneNumber = $phoneNumber;
    }
}
