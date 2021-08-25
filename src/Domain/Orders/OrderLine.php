<?php

namespace App\Domain\Orders;

use App\Domain\Items\Classes\Item;
use App\Domain\Orders\Exceptions\OrderLineException;

Class OrderLine
{
    use \App\Domain\Tools\Hydration;

    private $id;
    private Item $item;
    private int $quantity;
    private Order $order;

    private function __construct(array $data)
    {
        $this->hydrate($data);
    }

    // GETTERS

    public function id(): int
    {
        return $this->id;
    }

    public function order(): Order
    {
        return $this->order;
    }

    public function item(): Item
    {
        return $this->item;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    // SETTERS
    public function setId($id): void
    {
        $id = (int) $id;

        if ($id <= 0) 
        {
            throw new OrderLineException('Invalid id to orderLine');
            return;
        }

        $this->id = $id;
    }

    public function setOrder($order): void
    {
        if( !is_a( $order, get_class(new Order([])) ) ){
            throw new OrderLineException ("is not element to Order class");
            return;
        }

        if ($order->id() <= 0) 
        {
            throw new OrderLineException('Invalid id to orderLine');
            return;
        }

        $this->order = $order;
    }

    public function setItem($item): void
    {

        if( !is_a( $item, get_class(new Item([])) ) ){
            throw new OrderLineException ("is not element to Item class");
            return;
        }

        if($item->id() === null || $item->id() <= 0 )
        {
            throw new OrderLineException ("Invalid item");
            return;
        }

        $this->item = $item;
    }

    public function setQuantity($quantity): void
    {
        $quantity= (int) $quantity;

        if($quantity <= 0 || $quantity > 100){
            throw new OrderLineException ("Invalid quantity to orderLine");
            return;
        }

        if($quantity > $this->item()->stock()){
            throw new OrderLineException ("Quantity greater than the item stock");
            return;
        }

        $this->quantity = $quantity;
    }

    // methods
    public function getCost(): int
    {
        return $this->quantity() * $this->item()->price();
    }

    public function __serialize(): array
    {
        return array(
            "id"=>$this->id(),
            "item"=> $this -> item(),
            "quantity" => $this -> quantity(),
            "order"=>$this -> order()->id()
        );
    }

    public function __unserialize(array $data)
    {
        return new self($data);
    }
}

