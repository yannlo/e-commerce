<?php


namespace App\Domain\Items\Classes;

use App\Domain\Pictures\Picture;
use App\Domain\Accounts\Classes\Distributor;
use App\Domain\Items\Classes\Exceptions\ItemException;


class Item 
{
    use \App\Domain\Tools\Hydration;

    private int $id;
    private Picture $picture;
    private string $itemName;
    private int $stock;
    private int $price;
    private Distributor $distributor;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }


    // GETTERS
    public function id():int
    {
        return $this->id;
    }

    public function picture(): picture
    {
        return $this->picture;
    }

    public function itemName():string
    {
        return $this->itemName;
    }

    public function stock():int
    {
        return $this->stock;
    }

    public function price():int
    {
        return $this->price;
    }

    public function distributor():Distributor
    {
        return $this->distributor;
    }


    //SETTERS
    public function setId($id): void
    {
        $id = (int) $id;

        if($id <=0)
        {
            throw new ItemException("Invalid id to item");
            return; 
        }

        $this-> id = $id;
    }

    public function setItemName($itemName): void
    {
        $itemName= (string) $itemName;

        if(strlen($itemName)<3)
        {
            throw new ItemException("Invalid name to item");
            return;
        }

        $this-> itemName = $itemName;
    }

    public function setPrice($price): void
    {
        $price = (int) $price;

        if($price <=0)
        {
            throw new ItemException("Invalid number to price");
            return; 
        }

        $this-> price = $price;
    }

    public function setStock( $stock): void
    {
        $stock = (int) $stock;

        if($stock <=0 || $stock >1000)
        {
            throw new ItemException("Invalid quantity to stock");
            return; 
        }

        $this-> stock = $stock;
    }

    public function setDistributor($distributor): void
    {
        if(!is_a($distributor,get_class(new Distributor([])) ))
        {
            throw new ItemException("Parameter is not distributor");
            return; 
        }

        if($distributor->id() <=0)
        {
            throw new ItemException("Invalid id to distributor");
            return; 
        }

        $this-> distributor = $distributor;
    }

    public function setPicture($picture): void
    {
        // code
    }
    
}