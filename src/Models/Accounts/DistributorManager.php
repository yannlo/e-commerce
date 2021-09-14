<?php

namespace App\Models\Accounts;

use App\Domain\Items\Classes\Item;
use App\Domain\Accounts\Classes\Distributor;

class DistributorManager
{
    public function __construct(private \PDO $db)
    { }

    public function getAll(): array
    {
        $request = $this -> db -> query("SELECT * FROM distributors");
        $table = array();
        while($data = $request->fetch())
        {
            $table[] = new Distributor($data);
        }
        return $table;
    }

    public function getOnce(int $id): Distributor|bool
    {
        $request = $this -> db -> prepare("SELECT * FROM distributors  WHERE id = :id");
        try{
            $request ->execute(array(
                "id" => htmlspecialchars($id)
            ));
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
        return new Distributor($request->fetch(\PDO::FETCH_ASSOC));
    }


    public function getByItem(Item $item): Distributor|bool
    {
        $request = $this -> db -> prepare("SELECT * FROM items  WHERE id = :id");
        try{
            $request ->execute(array(
                "id" => htmlspecialchars($item->id())
            ));

            
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
        return $this->getOnce($request->fetch()['distributor']);
    }

    public function add(Distributor $distributor): bool
    {
        $request = $this -> db -> prepare("INSERT INTO distributors (id, nameDistrib, email, password, description) VALUES (:id, :nameDistrib, :email, :password, :description)");
        try
        {
            $request ->execute(array(
                "id" => htmlspecialchars($distributor-> id()),
                "nameDistrib" => htmlspecialchars($distributor -> nameDistrib()),
                "email" => htmlspecialchars($distributor -> email()),
                "password" => password_hash($distributor -> password(),PASSWORD_DEFAULT),
                "description" => htmlspecialchars($distributor -> description())
            ));

            return true;
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
    }

    public function update(Distributor $distributor): bool
    {
        $request = $this -> db -> prepare("UPDATE distributors SET nameDistrib = :nameDistrib, email= :email, password=:password, description = :description WHERE id = :id");
        try
        {
            $request ->execute(array(
                "id" => htmlspecialchars($distributor-> id()),
                "nameDistrib" => htmlspecialchars($distributor -> nameDistrib()),
                "email" => htmlspecialchars($distributor -> email()),
                "password" => password_hash( $distributor -> password(),PASSWORD_DEFAULT),
                "description" => htmlspecialchars($distributor -> description())
            ));

            return true;
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
    }

    public function delete(Distributor $distributor): bool
    {
        $request = $this -> db -> prepare("DELETE FROM distributors WHERE id = :id");
        try
        {
            $request ->execute(array(
                "id" => $distributor-> id(),
            ));

            return true;
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
    }
}