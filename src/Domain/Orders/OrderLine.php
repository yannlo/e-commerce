<?php

namespace App\Domain\Orders;

use App\Domain\Items\Classes\Item;
use App\Domain\Orders\Exceptions\OrderLineException;

/**
 * OrderLine
 * 
 * represent orderline in cart and in orderByDistributor
 */
Class OrderLine
{
    use \App\Domain\Tools\Hydration;

    private $id;
    private Item $item;
    private int $quantity;
    private ?Cart $cart;
    private ?OrderByDistributor $orderByDistributor;

    public function __construct(array $data)
    {
        $this->hydrate($data);
    }

    // GETTERS    
    /**
     * id
     * 
     * get id value
     *
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }
    
    /**
     * item
     * 
     * get item
     *
     * @return Item
     */
    public function item(): Item
    {
        return $this->item;
    }
    
    /**
     * quantity
     * 
     * get qunatity value
     *
     * @return int
     */
    public function quantity(): int
    {
        return $this->quantity;
    }
    
    /**
     * cart
     * 
     * get Cart
     *
     * @return Cart
     */
    public function cart(): ?Cart
    {
        return $this->cart;
    }
    
    /**
     * orderByDistributor
     * 
     * get orderByDistributor
     *
     * @return OrderByDistributor
     */
    public function orderByDistributor(): ?OrderByDistributor
    {
        return $this->orderByDistributor;
    }

    // SETTERS    
    /**
     * setId
     * 
     * set id value
     *
     * @param  int $id
     * @return void
     */
    public function setId($id): void
    {
        $id = (int) $id;

        if ($id < 0) 
        {
            throw new OrderLineException('Invalid id to orderLine',101);
            return;
        }

        $this->id = $id;
    }
    
    /**
     * setItem
     *
     * set item to this order line
     * 
     * @param  Item $item
     * @return void
     */
    public function setItem($item): void
    {

        if( !is_a( $item, get_class(new Item([])) ) ){
            throw new OrderLineException ("is not element to Item class",301);
            return;
        }

        if($item->id() === null || $item->id() <= 0 )
        {
            throw new OrderLineException ("Invalid item",101);
            return;
        }

        $this->item = $item;
    }
    
    /**
     * setQuantity
     *
     * set quantity value
     * 
     * @param  int $quantity
     * @return void
     */
    public function setQuantity($quantity): void
    {
        $quantity= (int) $quantity;

        if($quantity <= 0 || $quantity > 100){
            throw new OrderLineException ("Invalid quantity to orderLine",101);
            return;
        }

        if($quantity > $this->item()->stock()){
            throw new OrderLineException ("Quantity greater than the item stock",101);
            return;
        }

        $this->quantity = $quantity;
    }
    
    /**
     * setCart
     *
     * set cart to this order line and unset orderByDistributor
     * 
     * @param  Cart $cart
     * @return void
     */
    public function setCart($cart): void
    {
        if( !is_a( $cart, get_class(new Cart([])) ) ){
            throw new OrderLineException ("is not element to Cart class",301);
            return;
        }

        if(isset($this->orderByDistributor)){
            throw new OrderLineException ("order by distributor is not null",600);
            return;
        }

        $this->orderByDistributor =null;

        $this->cart = $cart;
    }

    /**
     * setOrderByDistributor
     *
     * set orderByDistributor to this order line and unset cart
     * 
     * @param  OrderByDistributor $orderByDistributor
     * @return void
     */
    public function setOrderByDistributor($orderByDistributor): void
    {
        if( !is_a( $orderByDistributor, get_class(new OrderByDistributor([])) ) ){
            throw new OrderLineException ("is not element to OrderByDistributor class",301);
            return;
        }

        if(isset($this->orderByDistributor)){
            throw new OrderLineException ("cart is not null",600);
            return;
        }

        $this->cart = null;

        $this->orderByDistributor = $orderByDistributor;
    }

    // methods    
    /**
     * getCost
     * 
     * permit to get cost to this order line
     *
     * @return int
     */
    public function getCost(): int
    {
        return $this->quantity() * $this->item()->price();
    }

}

