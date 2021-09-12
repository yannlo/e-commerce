<?php

namespace App\Models\Tools\Classes;

use App\Domain\Accounts\Classes\Account;
use App\Models\Tools\Classes\Exceptions\LoginVerificationException;

class LoginVerification{

    private array $accountType;


    public function __construct(private \PDO $db){
        $this->accountType =["distributor","customer"];
    }


    public function verify_email(Account $account)
    {
        if(!$this->email_exist($account))
        {   
            throw new LoginVerificationException ('email invalide',501);
            return ;
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
        throw new LoginVerificationException ('password invalide',501);
        return;
    }

    public function account_verify(Account $account)
    {
        $result = $this -> verify_email($account);

        $account_founded = $this->verify_password($result, $account);

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
        $dataBaseName =GetDataBase::ByAccountType($account,$this->accountType);
        $request = $this -> db -> prepare("SELECT * FROM ".$dataBaseName." WHERE email = :email ");
        $request ->execute(array(
            "email" => $account-> email()
        ));

        return $request;
    }

}