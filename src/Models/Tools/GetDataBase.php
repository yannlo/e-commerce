<?php

namespace App\Models\Tools;

use App\Domain\Accounts\Classes\Account;

class GetDataBaseName
{

    public static function ByAccountType(Account $account, array $accountTypeList)
    {
        $dataBaseName = "";   
        
        $nameClasse="";
        foreach($accountTypeList as  $endNameClasse){
            $nameClasse = "App\\Domain\\Accounts\\Classes\\".$endNameClasse;
            
            if(is_a($account ,$nameClasse)){
                $dataBaseName = $endNameClasse."s";
                break;
            }
        }
        return $dataBaseName;
    }
}