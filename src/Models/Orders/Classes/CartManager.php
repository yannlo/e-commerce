<?php

namespace App\Models\Orders\Classes;

use App\Domain\Orders\Cart;
use App\Domain\Accounts\Classes\Customer;
use App\Models\Orders\Classes\Exceptions\CartManagerException;


/**
 * CartInMySQL
 * 
 * CRUD operation to Cart table
 * 
 */
class CartManager
{
    public function __construct(private \PDO $db)
    { }
    
    // CRU
    /**
     * getByCustomer
     * 
     * get cart by customer
     *
     * @param  Customer $customer
     * @return null|Cart
     */
    public function get(Customer $customer): ?Cart
    {
        $request = $this-> db -> prepare("SELECT id FROM carts  WHERE customer= :customer");

        try
        {
            $request ->execute(array(
                "customer" => $customer->id()
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new CartManagerException("Recovery cart error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }


        if($request->rowCount()===0)
        {
            $exception= new CartManagerException("Never cart to this customer",500);
            throw $exception;
            return null;
        }


        if($request->rowCount()>1)
        {
            $exception= new CartManagerException("inconsistent result",800);
            throw $exception;
            return null;
        }

        $data = $request->fetch(\PDO::FETCH_ASSOC);
        $data['customer'] = $customer;
        
        return new Cart($data);

    }

    /**
     * add
     * 
     * add cart in database
     *
     * @param  Cart $cart
     * @return void
     */
    public function add(Cart $cart): void
    {
        $request = $this-> db -> prepare("INSERT INTO carts  (customer) VALUES (:customer)");
        try
        {
            $request ->execute(array(
                "customer" => $cart->customer()->id()
            ));  
        }

        catch(\PDOException $e)
        {   
            $exception= new CartManagerException("Recovery orderLine error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }

    }
    
    /**
     * delete
     *
     * delete cart in database
     * 
     * @param Cart $cart
     * @return void
     */
    public function delete(Cart $cart): void
    {
        $request = $this-> db -> prepare("DELETE FROM carts WHERE id= :id");

        try
        {
            $request ->execute(array(
                "id" => $cart->id()
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new CartManagerException("Recovery orderLine error in the database", 700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
    }
    
    /**
     * cartExist
     * 
     * verif if cart to this customer exist
     *
     * @param  Customer $customer
     * @return bool
     * 
     * **true** if cart exist
     * **false** if cart doesn't exist
     * 
     */
    public function cartExist(Customer $customer): ?bool
    {
        $request = $this-> db -> prepare("SELECT * FROM carts  WHERE customer= :customer");

        try
        {
            $request ->execute(array(
                "customer" => $customer->id()
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new CartManagerException("Recovery cart error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()>1)
        {
            $exception= new CartManagerException("Never orderLine to this order",800);
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            return false;
        }

        return true;

    }
}
