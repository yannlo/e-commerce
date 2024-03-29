<?php

namespace App\Models\Accounts;

use App\Domain\Accounts\Classes\Customer;

class CustomerManager 
{
    public function __construct(private \PDO $db)
    { }

    public function getAll(): array
    {
        $request = $this -> db -> query("SELECT * FROM customers");
        $table = array();
        while($data = $request->fetch())
        {
            $table[] = new Customer($data);
        }
        return $table;
    }

    public function getOnce(int $id): Customer|bool
    {
        $request = $this -> db -> prepare("SELECT * FROM customers  WHERE id = :id");
        try{
        $request ->execute(array(
            "id" => $id,
        ));
        return new Customer($request->fetch(\PDO::FETCH_ASSOC));
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
    }

    public function add(Customer $customer): bool
    {
        $request = $this -> db -> prepare("INSERT INTO customers (id, firstName, lastName, email, password) VALUES (:id, :firstName, :lastName, :email, :password)");
        try
        {
            $request ->execute(array(
                "id" => $customer-> id(),
                "firstName" => htmlspecialchars( $customer -> firstName()),
                "lastName" => htmlspecialchars($customer -> lastName()),
                "email" => htmlspecialchars($customer -> email()),
                "password" => password_hash ($customer -> password(),PASSWORD_DEFAULT)
            ));

            return true;
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
    }

    public function update(Customer $customer): bool
    {
        $request = $this -> db -> prepare("UPDATE customers SET firstName = :firstName, lastName = :lastName, email= :email, password=:password WHERE id = :id");
        try
        {
            $request ->execute(array(
                "id" =>  $customer-> id(),
                "firstName" => htmlspecialchars($customer -> firstName()),
                "lastName" =>htmlspecialchars($customer -> lastName()),
                "email" =>htmlspecialchars( $customer -> email()),
                "password" =>password_hash ($customer -> password(),PASSWORD_DEFAULT)
            ));

            return true;
        }
        catch(\PDOException $e)
        {
            echo $e -> getMessage();
            return false;
        }
    }

    public function delete(Customer $customer): bool
    {
        $request = $this -> db -> prepare("DELETE FROM customers WHERE id = :id");
        try
        {
            $request ->execute(array(
                "id" => $customer-> id(),
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