<?php


namespace App\Domain\Items\Classes;

use App\Domain\Items\Classes\Exceptions\ItemException;


class Item 
{
    use \App\Domain\Tools\Hydration;

    private int $id;
    protected string $itemName;
    private int $stock;
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

    public function stock():int
    {
        return $this->stock;
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

    public function setIdDistrib($idDistrib): void
    {
        $idDistrib = (int) $idDistrib;

        if($idDistrib <=0)
        {
            throw new ItemException("Invalid id to distributer");
            return; 
        }

        $this-> idDistrib = $idDistrib;
    }

    public function jsonEncoder()
    {
        $data=[
            'id' =>$this->id(),
            'itemName'=>$this->itemName(),
            'stock' =>$this->stock(),
            'price' =>$this-> price(),
            'idDistrib' =>$this->idDistrib()
        ];

        return json_encode($data,JSON_FORCE_OBJECT);
    }


    public static function jsonDecoder(string $json)
    {
        $data = (array)json_decode($json);
        return new self($data);
    }
    
}