<?php

namespace App\Models\Classes\Account;

use App\Domain\Classes\Accounts\Customer;

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

    public function verify_email(Customer $customer)
    {
        $request = $this -> db -> prepare("SELECT * FROM customers  WHERE email = :email");
        $request ->execute(array(
            "email" => $customer-> email()
        ));
        $count=$request->rowCOUNT();
        if($count==0){

            return false; 
        }
        
        $table = array();
        while($data = $request->fetch())
        {
            $table[] = new Customer($data);
        }
        return $table;
    }

    private function verify_password(array $customers,Customer $customer){
        foreach($customers as $customer_found){
            if( !password_verify($customer->password(), $customer_found->password()) ){
                return["error"=>"password","message"=>"password invalide"];
            }
            else{
                return $customer_found;
            }
        }
    }

    public function customer_verify(Customer $customer)
    {
        $table = $this -> verify_email($customer);
        if( $table == false){
            return ["error"=>"email","message"=>"email invalide"];    
        }

        $customer_founded = $this->verify_password($table, $customer);
        if(!is_object($customer_founded))
        {
            return ["error"=>"password","message"=>"password invalide"];    
        }

        return $customer_founded;

    }


}