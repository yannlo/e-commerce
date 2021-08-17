<?php
declare(strict_types=1);

namespace App\Domain\Classes;
use App\Domain\Classes\OthersException\ItemException;

class Item 
{
    use \App\Domain\Traits\Hydration;

    private int $id=0;
    private string $itemName="";
    private int $price=0;
    private int $idDistrib=0;

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
        if(!is_int($id))
        {
            throw new ItemException("l'id n'est pas un entier");
            return;
        }

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
        if(!is_int($price))
        {
            throw new ItemException("le prix n'est pas un entier");
            return;
        }

        if($price <=0)
        {
            throw new ItemException("le prix n'est pas strictement positive");
            return; 
        }

        $this-> price = $price;
    }

    public function setIdDistrib($idDistrib): void
    {
        if(!is_int($idDistrib))
        {
            throw new ItemException("l'id du distributeur n'est pas un entier");
            return;
        }

        if($idDistrib <=0)
        {
            throw new ItemException("l'id du distributeur n'est pas strictement positive");
            return; 
        }

        $this-> idDistrib = $idDistrib;
    }


}