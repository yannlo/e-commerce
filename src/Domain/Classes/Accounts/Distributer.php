<?php
declare(strict_types=1);

namespace App\Domain\Classes\Accounts;
use App\Domain\Classes\Account\Exceptions\DistributerException;

class Distributer extends DefaultAccount
{
    use \App\Domain\Traits\Hydration;

    private string $nameDistrib="";
    private string $description="";

    // constructor
    public function __construct(array $data){
        parent::__construct($data);
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

    //SETTERS
    public function setNameDistrib($name)
    {
        $name= (string) $name;
        if(strlen($name)<4)
        {
            throw new DistributerException("le nom saisi ne contient pas plus de 3 caracteres");
            return; 
        }
        
        $this->nameDistrib = $name;

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