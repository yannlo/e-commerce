<?php


namespace App\Domain\Accounts\Classes;
use App\Domain\Accounts\Classes\Exceptions\DistributerException;

class Distributer extends Account
{

    private string $nameDistrib;
    private string $description;
    private array $deliveryType=[];
    private array $paymentType=[];

    // CONSTANTS
        // Delivery constant
    const GO_TO_STORE = 0;
    const HOME_DELIVERY = 1;
    const MEETING_POINT_DELIVERY= 2;

        // Payment constant
    const BY_CARD = 10;
    const BY_MTN_MONEY = 11;
    const BY_ORANGE_MONEY = 12;
    const BY_PAYPAL = 13;
    const ON_DELIVERY = 14;

    // constructor
    public function __construct(array $data){
        $this->hydrate($data);
    }

    // GETTERS
    public function nameDistrib(): string
    {
        return $this->nameDistrib;
    }

    public function description(): string
    {
        return $this->description;
    }


    // SETTERS
    public function setNameDistrib($nameDistrib)
    {
        $nameDistrib= (string) $nameDistrib;

        if(strlen($nameDistrib)<4)
        {
            throw new DistributerException("le nom saisi ne contient pas plus de 3 caracteres");
            return; 
        }
        
        $this->nameDistrib = $nameDistrib;

    }

    public function setDescription($description)
    {
        $description= (string) $description;
        
        if(strlen($description)<0)
        {
            throw new DistributerException("Veuillez saisir une description");
            return;
        }

        if(strlen($description)>1024)
        {
            throw new DistributerException("La description est trop long");
            return;
        }

        $this->description= $description;
    }



}