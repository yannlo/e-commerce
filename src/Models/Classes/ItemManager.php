<?php

namespace App\Models\Classes;

use \App\Domain\Classes\Item;
use \App\Models\Tools\Connect_DB;


class ItemManager
{

    public function __construct (private \PDO $db)
    { }

    public function getAll(): array
    {
        $request = $this -> db -> query("SELECT * FROM items");
        $table =[];
        while($data = $request->fetch()){
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
        $request = $this -> db -> prepare("INSERT INTO items (id, itemName, price, idDistrib) VALUES (:id, :itemName, :price, :idDistrib)");
        try {

            $request->execute(array(
                "id"=>$item -> id(),
                "itemName" => $item -> itemName(),
                "price" => $item -> price(),
                "idDistrib" => $item -> idDistrib()
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
        $request = $this -> db -> prepare("UPDATE items SET itemName= :itemName, price=:price, idDistrib=:idDistrib WHERE id=:id");
        try{

            $request->execute(array(
                "id"=>$item -> id(),
                "itemName" => $item -> itemName(),
                "price" => $item -> price(),
                "idDistrib" => $item -> idDistrib()
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