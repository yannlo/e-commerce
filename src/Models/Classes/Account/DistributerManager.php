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
    public function verify_email(Distributer $distributer)
    {
            
        $request = $this -> db -> prepare("SELECT * FROM distributers  WHERE email = :email");
        $request ->execute(array(
            "email" => $distributer-> email()
        ));
        $count=$request->rowCOUNT();
        if($count==0){
            dd($count);
            return false; 
        }
        
        $table = array();
        while($data = $request->fetch())
        {
            $table[] = new Distributer($data);
        }
        return $table;
    }

    private function verify_password(array $distributers,Distributer $distributer){
        foreach($distributers as $distributer_found){
            if( !password_verify($distributer->password(), $distributer_found->password()) ){
                return["error"=>"password","message"=>"password invalide"];
            }
            else{
                return $distributer_found;
            }
        }
    }

    public function distributer_verify(Distributer $distributer)
    {
        $table = $this -> verify_email($distributer);
        if( $table == false){
            return ["error"=>"email","message"=>"email invalide"];    
        }

        $distributer_founded = $this->verify_password($table, $distributer);
        if(!is_object($distributer_founded))
        {
            return ["error"=>"password","message"=>"password invalide"];    
        }

        return $distributer_founded;

    }
}