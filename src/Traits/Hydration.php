<?php

namespace App\Traits;

trait Hydration{
    //hydratation
    protected function hydrate(array $data){
        foreach($data as $key =>$value){
            $method = "set".ucfirst($key);
            if(method_exists($this, $method))
            {
                $this->$method($value);
            }
        }
    }
}