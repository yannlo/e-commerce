<?php

namespace App\Models\Orders\Classes\SaveCarts;

use App\Domain\Orders\Cart;
use App\Domain\Tools\JSONFormatter;
use App\Models\Orders\Interfaces\CartCRUD;
use App\Models\Orders\Classes\Exceptions\CookiesException;

/**
 * CookiesToSave
 * 
 * use to execute CRUD operations on cart in cookies
 */
class Cookies implements  CartCRUD
{    
    /**
     * getCart
     * 
     * return cart save in cookies
     *
     * @return void
     */
    public function get($customer = null): ?Cart
    {
        if(!$this->cartExist())
        {
            throw new CookiesException("Cart not exist",600);
            return null;
        }

        return JSONFormatter::jsonDecoderToOrder($_COOKIE["cart"]);
    }
    
    /**
     * add
     * 
     * Add cart in cookies
     *
     * @param  Cart $cart
     * @return void
     */
    public function add(Cart $cart): void
    {
        $stringOrder = JSONFormatter::jsonEncoderToCart($cart);
        
        $savedCookies=setcookie(
            name:"cart",
            value: $stringOrder,
            expires_or_options: time() + (60 * 60 * 24 * 7 * 4)
        );
        
        if(!$savedCookies)
        {
            throw new CookiesException("Cookies is not saved",700);
            return;
        }

        $_COOKIE["cart"]= $stringOrder;

    }
    
    /**
     * update
     *
     * Update cart in cookies if exist
     * 
     * @param  Cart $cart
     * @return void
     */
    public function update(Cart $cart): void
    {

        if(!$this->cartExist())
        {
            throw new CookiesException("cart cookies not exist",600);
            return;
        }

        $stringOrder = JSONFormatter::jsonEncoderToCart($cart);

        $resultCookies=setcookie(
            name:"cart",
            value: $stringOrder,
            expires_or_options: time() + (60 * 60 * 24*31)
        );
 
        if($resultCookies===false)
        {
            throw new CookiesException("Cookies is not saved",700);
            return;
        }

        $_COOKIE["cart"]= $stringOrder;

    }
    
    /**
     * delete
     *
     * Delete cart in cookies
     * 
     * @return void
     */
    public function delete($cart = null): void
    {
        $resultCookies=setcookie(
            name:"cart",
            value: "",
            expires_or_options: time()-1
        );

        if(!$resultCookies)
        {
            throw new CookiesException("Cookies is not delete",700);
            return;
        }

        unset($_COOKIE["cart"]);
    }
    
    /**
     * cartExist
     *
     * Verif if cart exist in cookies
     * 
     * @return bool
     * **false** if card doesn't exist and **true** if card exist
     * 
     */
    public function cartExist(): bool
    {
        if(!isset($_COOKIE["cart"]) || empty($_COOKIE["cart"]))
        {
            return false;
        }

        return true;
    }
}