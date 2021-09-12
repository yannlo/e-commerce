<?php

namespace App\Models\Orders\Classes\SaveCarts;

use App\Domain\Orders\Cart;
use App\Domain\Accounts\Classes\Customer;
use App\Models\Orders\Classes\CartManager;
use App\Models\Orders\Interfaces\CartCRUD;
use App\Domain\Orders\Exceptions\CartException;
use App\Models\Orders\Classes\OrderLineManager;
use App\Models\Orders\Classes\InMySQL\CartInMySQL;
use App\Models\Orders\Classes\Exceptions\SaveCarts\MySQLException;

class MySQL implements CartCRUD
{
    private CartManager $cartManager;
    private OrderLineManager $orderLineManager;

    public function __construct(private \PDO $db)
    {
        $this->cartManager = new CartManager($this->db);
        $this -> orderLineManager = new OrderLineManager($this->db);

    }

        
    /**
     * get
     * 
     * permit to get a complet cart
     *
     * @param  null|Customer $customer
     * @return Cart
     */
    public function get(?Customer $customer): ?Cart
    {
        if(!$this->cartManager->cartExist($customer))
        {
            throw new MySQLException('this customer has not cart',400);
            return null;
        }

        $cart = $this->cartManager-> get($customer);

        try {
            $cart->setOrderLines($this->orderLineManager->getByCart($cart));
        }
        finally
        {
            return $cart;
        }
        
        
    }
        
    /**
     * add
     *
     * add complet cart in database
     * 
     * @param  Cart $cart
     * @return void
     */
    public function add(Cart $cart): void
    {
        $this->cartManager->add($cart);

        $cartAdded = $this->cartManager->get($cart->customer());

        if(!empty($cart->orderLines()))
        {
            $cartAdded->setOrderLines($cart->orderLines());
        }

        foreach ($cartAdded->orderLines() as $orderLine) {
            $this->orderLineManager->add($orderLine);
        }

    }
    
    /**
     * update
     * 
     * update complet cart in database
     *
     * @param  Cart $cart
     * @return void
     */
    public function update(Cart $cart): void
    {
        
        if(($this->get($cart->customer()))->id() !== $cart->id())
        {
            throw new MySQLException('this customer has not cart',501);
            return;
        }

        $lastCart = $this->get($cart->customer());
        foreach ($cart->orderLines() as $NewOrderLine) 
        {

            if($lastCart->orderLineExist($NewOrderLine)) {
                $this->orderLineManager->update($NewOrderLine);
                continue;
            }

            $this->orderLineManager->add($NewOrderLine);

        }


        foreach ($lastCart->orderLines() as $lastOrderLine)
        {
            if(!$cart->orderLineExist($lastOrderLine)) {
                $this->orderLineManager->delete($lastOrderLine);
            }
        }

        $finalCart = $this->get($cart->customer());

        try
        {
            $cart->setOrderLines($finalCart->orderLines());
        }
        catch(CartException $e)
        {
            if($e->getCode()!==200)
            {
                throw $e;
            }
        }
        finally
        {
            return;
        }

    }
    
    /**
     * delete
     *
     * delete complet order line in database
     * 
     * @param Cart $cart
     * @return void
     */
    public function delete(?Cart $cart): void
    {
        if(($this->get($cart->customer()))->id() === $cart->id())
        {
            throw new MySQLException('this cart not exist in database',501);
            return;
        }

        foreach ($cart->orderLines() as $orderLine) {
            $this->orderLineManager->delete($orderLine);
        }

        $this->cartManager->delete($cart);
    }
    
    /**
     * cartExist
     * 
     * verif if orderline exist in database
     *
     * @param  Customer $customer
     * @return bool
     */
    public function cartExist(Customer $customer): bool
    {   
        return $this->cartManager->cartExist($customer);;
    }
}