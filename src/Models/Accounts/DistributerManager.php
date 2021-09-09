<?php

namespace App\Models\Accounts;

use App\Domain\Items\Classes\Item;
use App\Domain\Accounts\Classes\Distributer;

class DistributerManager
{
    public function __construct(private \PDO $db)
    { }

    public function getAll(): array
    {
        $request = $this -> db -> query("SELECT * FROM distributers");
        $table = array();
        while($data = $request->fetch())
        {
            $table[] = new Distributer($data);
        }
        return $table;
    }

    public function getOnce(int $id): Distributer|bool
    {
        $request = $this -> db -> prepare("SELECT * FROM distributers  WHERE id = :id");
        try{
        $request ->execute(array(
            "id" => htmlspecialchars($id)
        ));
        return new Distributer($request->fetch(\PDO::FETCH_ASSOC));
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
    }


    public function getByItem(Item $item): Distributer|bool
    {
        $request = $this -> db -> prepare("SELECT * FROM items  WHERE id = :id");
        try{
        $request ->execute(array(
            "id" => htmlspecialchars($item->id())
        ));

        return $this->getOnce($request->fetch()['distributer']);
        
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
    }

    public function add(Distributer $distributer): bool
    {
        $request = $this -> db -> prepare("INSERT INTO distributers (id, nameDistrib, email, password, description) VALUES (:id, :nameDistrib, :email, :password, :description)");
        try
        {
            $request ->execute(array(
                "id" => htmlspecialchars($distributer-> id()),
                "nameDistrib" => htmlspecialchars($distributer -> nameDistrib()),
                "email" => htmlspecialchars($distributer -> email()),
                "password" => password_hash($distributer -> password(),PASSWORD_DEFAULT),
                "description" => htmlspecialchars($distributer -> description())
            ));

            return true;
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
    }

    public function update(Distributer $distributer): bool
    {
        $request = $this -> db -> prepare("UPDATE distributers SET nameDistrib = :nameDistrib, email= :email, password=:password, description = :description WHERE id = :id");
        try
        {
            $request ->execute(array(
                "id" => htmlspecialchars($distributer-> id()),
                "nameDistrib" => htmlspecialchars($distributer -> nameDistrib()),
                "email" => htmlspecialchars($distributer -> email()),
                "password" => password_hash( $distributer -> password(),PASSWORD_DEFAULT),
                "description" => htmlspecialchars($distributer -> description())
            ));

            return true;
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
    }

    public function delete(Distributer $distributer): bool
    {
        $request = $this -> db -> prepare("DELETE FROM distributers WHERE id = :id");
        try
        {
            $request ->execute(array(
                "id" => $distributer-> id(),
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