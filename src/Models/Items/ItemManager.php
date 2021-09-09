<?php

namespace App\Models\Items;

use App\Domain\Items\Classes\Item;


class ItemManager
{

    public function __construct (private \PDO $db)
    { }

    public function getAll(): array
    {
        $request = $this -> db -> query("SELECT * FROM items");
        $table =[];
        while($data = $request->fetch()){
            unset($data['distributer']);
            $table[]= new Item($data);
        }
        return $table;
    }

    public function getOnce(int $id): Item
    {
        $request = $this -> db -> prepare("SELECT * FROM items WHERE id =:id");
        $request->execute(array(
            "id"=>$id
        ));

        return new Item($request->fetch());
    }

    public function add(Item $item): bool
    {
        $request = $this -> db -> prepare("INSERT INTO items (itemName, price, stock, distributer) VALUES (:itemName, :price, :stock, :distributer)");
        try {

            $request->execute(array(
                "itemName" => $item -> itemName(),
                "price" => $item -> price(),
                "stock" => $item -> stock(),
                "distributer" => $item -> distributer()
            ));
            return true;
        }
        catch(\PDOException $e){
            echo $e-> getMessage();
            return false;
        }
        
    }

    public function update(Item $item): bool
    {
        $request = $this -> db -> prepare("UPDATE items SET itemName= :itemName, price=:price,stock=:stock, distributer=:distributer WHERE id=:id");
        try{

            $request->execute(array(
                "id"=>$item -> id(),
                "itemName" => $item -> itemName(),
                "price" => $item -> price(),
                "price" => $item -> stock(),
                "distributer" => $item -> distributer()
            ));
            return true;
        }
        catch(\PDOException $e){
            echo $e-> getMessage();
            return false;
        }
    }

    public function delete(Item|int $item): bool
    {
        $request = $this -> db -> prepare("DELETE FROM items WHERE id=:id");
        try{
            $request->execute(array(
                "id"=>$item -> id()
            ));
            return true;
        }
        catch(\PDOException $e){
            echo $e-> getMessage();
            return false;
        }
    }


}