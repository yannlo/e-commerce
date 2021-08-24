<?php


namespace App\Domain\Items\Classes;
use App\Domain\Classes\Items\Exceptions\ItemException;

class Item 
{
    use \App\Domain\Tools\Hydration;

    private int $id;
    private string $itemName;
    private int $price;
    private int $idDistrib;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }


    // GETTERS
    public function id():int
    {
        return $this->id;
    }

    public function itemName():string
    {
        return $this->itemName;
    }

    public function price():int
    {
        return $this->price;
    }

    public function idDistrib():int
    {
        return $this->idDistrib;
    }

    //SETTERS
    public function setId($id): void
    {
        $id = (int) $id;

        if($id <=0)
        {
            throw new ItemException("l'id n'est pas strictement positive");
            return; 
        }

        $this-> id = $id;
    }

    public function setItemName($itemName): void
    {
        $itemName= (string) $itemName;

        if(strlen($itemName)<3)
        {
            throw new ItemException("le nom est trop court");
            return;
        }

        $this-> itemName = $itemName;
    }

    public function setPrice($price): void
    {
        $price = (int) $price;

        if($price <=0)
        {
            throw new ItemException("le prix n'est pas strictement positive");
            return; 
        }

        $this-> price = $price;
    }

    public function setIdDistrib($idDistrib): void
    {
        $idDistrib = (int) $idDistrib;

        if($idDistrib <=0)
        {
            throw new ItemException("l'id du distributeur n'est pas strictement positive");
            return; 
        }

        $this-> idDistrib = $idDistrib;
    }

    
}