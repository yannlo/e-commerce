<?php

namespace App\Models\Items;

use App\Domain\Items\Classes\Item;
use App\Models\Accounts\DistributorManager;
use App\Domain\Accounts\Classes\Distributor;
use App\Models\Item\Exceptions\ItemManagerException;

class ItemManager
{
    private DistributorManager $distributorManager;

    public function __construct (private \PDO $db)
    {
        $this->distributorManager = new DistributorManager($this->db);
    }

    // CRUD
    // get
    /**
     * getAll
     * 
     * permit to get all item in database
     *
     * @return array
     */
    public function getAll(): array
    {
        $request = $this -> db -> query("SELECT * FROM items");

        $table =[];

        while($data = $request->fetch()){
            $data['distributor'] = $this->distributorManager->getOnce($data['distributor']);
            $table[]= new Item($data);
        }
        return $table;
    }
    
    /**
     * getOnce
     * 
     * permit to get specific item in database
     *
     * @param  int $id
     * @return Item
     */
    public function getOnce(int $id): Item
    {
        $request = $this -> db -> prepare("SELECT * FROM items WHERE id =:id");
        try{
            $request->execute(array(
                "id"=>$id
            ));
        }
        catch (\PDOException $e)
        {
            $exception= new ItemManagerException("Recovery item error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            throw  new ItemManagerException("Never orderByDIstributor to this order",500);
            return null;
        }

        $data = $request->fetch();
        $data['distributor'] = $this->distributorManager->getOnce($data['distributor']);
        return new Item($data);
    }
    
    /**
     * getByDistributor
     * 
     * permit to get item array by distributor
     *
     * @param  Distributor $distributor
     * @return array
     */
    public function getByDistributor(Distributor $distributor): ?array
    {
        $request = $this -> db -> prepare("SELECT * FROM items WHERE distributor =:distributor");

        try{
            $request->execute(array(
                "distributor"=>$distributor->id()
            ));
        }
        catch (\PDOException $e)
        {
            $exception= new ItemManagerException("Recovery item error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            throw  new ItemManagerException("Never orderByDIstributor to this order",500);
            return null;
        }

        $items = [];
        while($data = $request->fetch())
        {
            $data['distributor'] = $distributor;
            $items[] = new Item($data);
        }
        return $items;
    }

    // add    
    /**
     * add
     *
     *  permit to add new item in database
     * 
     * @param  Item $item
     * @return void
     */
    public function add(Item $item): void
    {
        $request = $this -> db -> prepare("INSERT INTO items (itemName, price, stock, distributor) VALUES (:itemName, :price, :stock, :distributor)");
        try {

            $request->execute(array(
                "itemName" => $item -> itemName(),
                "price" => $item -> price(),
                "stock" => $item -> stock(),
                "distributor" => $item -> distributor()
            ));
        }
        catch(\PDOException $e){
            $exception= new ItemManagerException("Recovery item error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
        
    }

    //  update    
    /**
     * update
     *
     * permit to update item if it exists in database
     * 
     * @param  Item $item
     * @return void
     */
    public function update(Item $item): void
    {
        $request = $this -> db -> prepare("UPDATE items SET itemName= :itemName, price=:price,stock=:stock, distributor=:distributor WHERE id=:id");
        try{

            $request->execute(array(
                "id"=>$item -> id(),
                "itemName" => $item -> itemName(),
                "price" => $item -> price(),
                "price" => $item -> stock(),
                "distributor" => $item -> distributor()
            ));
        }
        catch(\PDOException $e){
            $exception= new ItemManagerException("Recovery item error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
    }

    // delete    
    /**
     * delete
     * 
     * permit to delete item in database
     *
     * @param  Item $item
     * @return void
     */
    public function delete(Item $item): void
    {
        $request = $this -> db -> prepare("DELETE FROM items WHERE id=:id");
        try{
            $request->execute(array(
                "id"=>$item -> id()
            ));
        }
        catch(\PDOException $e){
            $exception= new ItemManagerException("Recovery item error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
    }


}