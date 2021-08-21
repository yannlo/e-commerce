<?php

namespace App\Models\Classes\Account;

use App\Domain\Classes\Accounts\Distributer;

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
            "id" => $id,
        ));
        return new Distributer($request->fetch(\PDO::FETCH_ASSOC));
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
    }

    public function add(Distributer $distributer): bool
    {
        $request = $this -> db -> prepare("INSERT INTO distributers (id, nameDistib, email, password, description) VALUES (:id, :nameDistib, :email, :password, :description)");
        try
        {
            $request ->execute(array(
                "id" => $distributer-> id(),
                "nameDistib" => $distributer -> nameDistib(),
                "email" => $distributer -> email(),
                "password" => $distributer -> password(),
                "description" => $distributer -> description()
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
        $request = $this -> db -> prepare("UPDATE distributers SET nameDistib = :nameDistib, email= :email, password=:password, description = :description WHERE id = :id");
        try
        {
            $request ->execute(array(
                "id" => $distributer-> id(),
                "nameDistib" => $distributer -> nameDistib(),
                "email" => $distributer -> email(),
                "password" => $distributer -> password(),
                "description" => $distributer -> description()
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