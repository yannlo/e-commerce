<?php


namespace App\Models\Address;

use App\Models\Address\Exceptions\CommonListManagerException;

class CommonListManager
{

    public function __construct(private \PDO $db)
    {    }

    public function get(): array 
    {
        $request = $this->db->query("SELECT * FROM commons");


        return $request->fetchAll();
    }

    public function add(string $name, string $city): void
    {
        $request = $this->db->query("INSERT INTO commons (name, city) VALUES (:name, :city)");

        try
        {
            $request->execute(array(
                "name" => htmlSpecialChars($name),
                "city" => htmlSpecialChars($city)
            ));
        }
        catch (\PDOException $e)
        {
            $exception = new CommonListManagerException('Recovery Common error in the database');
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
        }

    }

    public function delete(string $name, string $city): void
    {
        $request = $this->db->query("DELETE FROM commons WHERE name=:name, city=:city");
        try
        {
            $request->execute(array(
                "name" => htmlSpecialChars($name),
                "city" => htmlSpecialChars($city)
            ));
        }
        catch (\PDOException $e)
        {
            $exception = new CommonListManagerException('Recovery Common error in the database');
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
        }

    }
}