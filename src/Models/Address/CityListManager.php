<?php


namespace App\Models\Address;

use App\Models\Address\Exceptions\CityListManagerException;

class CityListManager
{

    public function __construct(private \PDO $db)
    {    }

    public function get(): array 
    {
        $request = $this->db->query("SELECT * FROM cities");


        return $request->fetchAll();
    }

    public function add(string $name): void
    {
        $request = $this->db->query("INSERT INTO cities (name) VALUES (:name)");

        try
        {
            $request->execute(array(
                "name" => htmlSpecialChars($name)
            ));
        }
        catch (\PDOException $e)
        {
            $exception = new CityListManagerException('Recovery City error in the database');
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }

    }

    public function delete(string $name): void
    {
        $request = $this->db->query("DELETE FROM cities WHERE name=:name");
        try
        {
            $request->execute(array(
                "name" => htmlSpecialChars($name)
            ));
        }
        catch (\PDOException $e)
        {
            $exception = new CityListManagerException('Recovery City error in the database');
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }

    }

}