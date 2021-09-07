<?php


namespace App\Domain\Accounts\Classes;
use App\Domain\Accounts\Classes\Exceptions\DistributerException;

class Distributer extends Account
{

    private string $nameDistrib;
    private string $description;


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