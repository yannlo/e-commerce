<?php

namespace App\Models\Tools;

use App\Domain\Accounts\Classes\Account;

class LoginVerification{

    private array $accountType;


    public function __construct(private \PDO $db){
        $this->accountType =["distributer","customer"];
    }


    public function verify_email(Account $account)
    {
        if(!$this->email_exist($account))
        {
            return false;
        }   

        $table = array();
        $nameClasse = get_class($account);
        $request = $this->getAllEmailByAccount($account);
        while($data = $request->fetch())
        {
            $table[] = new $nameClasse ($data);
        }
        return $table;
    }

    private function verify_password(array $accounts, Account $account){
        foreach($accounts as $account_found){
            if( password_verify($account->password(), $account_found->password()) ){
                return $account_found;
            }
        }
        return false;
    }

    public function account_verify(Account $account)
    {
        $result = $this -> verify_email($account);
        if( $result == false){
            return ["error"=>"email","message"=>"email invalide"];    
        }

        $account_founded = $this->verify_password($result, $account);
        if($account_founded===false)
        {
            return ["error"=>"password","message"=>"password invalide"];    
        }

        return $account_founded;

    }

    public function email_exist(Account $account) :  bool
    {
        $count= $this->getAllEmailByAccount($account) -> rowCOUNT();
        if($count==0)
        {
            return false; 
        }
        
        return true;

    }

    private function getAllEmailByAccount(Account $account){
        $dataBaseName =GetDataBaseName::ByAccountType($account,$this->accountType);
        $request = $this -> db -> prepare("SELECT * FROM ".$dataBaseName." WHERE email = :email ");
        $request ->execute(array(
            "email" => $account-> email()
        ));

        return $request;
    }

}