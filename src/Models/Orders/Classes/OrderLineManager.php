<?php

namespace App\Models\Orders\Classes;

use App\Domain\Orders\Cart;
use App\Domain\Orders\OrderLine;
use App\Models\Items\ItemManager;
use App\Models\Tools\Classes\ConnectDB;
use App\Domain\Orders\OrderByDistributor;
use App\Models\Orders\Classes\Exceptions\OrderLineManagerException;



class OrderLineManager
{
    private ItemManager $itemManager;
    
    public function __construct(private \PDO $db)
    {
        $this->itemManager = new ItemManager(ConnectDB::getInstanceToPDO());

    }
    
    /**
     * getByCart
     *
     * return all orderline to the cart
     * 
     * @param  Cart $cart
     * @return null|array
     */
    public function getByCart(Cart $cart): ?array
    {
        $request = $this-> db -> prepare("SELECT * FROM orderlines  WHERE `cart`= :cart");

        try
        {
            $request ->execute(array(
                "cart" => $cart->id()
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new OrderLineManagerException("Recovery orderLine error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            $exception= new OrderLineManagerException("Never orderLine to this cart",500);
            throw $exception;
            return null;
        }

        $table=[];
        while($data = $request->fetch())
        {
            $data["item"] = $this->itemManager->getOnce($data['item']);
            unset($data['orderByDistributor']);
            $data["cart"] = $cart;
            $table[] = new OrderLine($data);
        }

        return $table;
    }

    /**
     * getByOrderByDistributor
     *
     * return all orderline to the OrderByDistributor
     * 
     * @param  OrderByDistributor $orderByDistributor
     * @return null|array
     */
    public function getByOrderByDistributor(OrderByDistributor $orderByDistributor): ?array
    {
        $request = $this-> db -> prepare("SELECT * FROM orderlines  WHERE `orderByDistributor`= :orderByDistributor");

        try
        {
            $request ->execute(array(
                "orderByDistributor" => $orderByDistributor->id()
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new OrderLineManagerException("Recovery orderLine error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return null;
        }

        if($request->rowCount()===0)
        {
            $exception= new OrderLineManagerException("Never orderLine to this order by distributor",500);
            throw $exception;
            return null;
        }

        $table=[];
        while($data = $request->fetch())
        {
            $data["item"] = $this->itemManager->getOnce($data['item']);
            unset($data['cart']);
            $data["orderByDistributor"] = $orderByDistributor;
            $table[] = new OrderLine($data);
        }

        return $table;
    }
    
    /**
     * add
     * 
     * add order line in database
     *
     * @param  OrderLine $orderLine
     * @return void
     */
    public function add(OrderLine $orderLine): void
    {
        $request = $this-> db -> prepare("INSERT INTO orderlines (item,quantity,cart,orderByDistributor) VALUES(:item,:quantity,:cart,:orderByDistributor)");
        try
        {

            $request ->execute(array(
                'item'=> $orderLine->item()->id(),
                'quantity'=> $orderLine->quantity(),
                'cart'=> ($orderLine->cart()!= null) ? $orderLine->cart()->id() : null,
                'orderByDistributor'=> ($orderLine->orderByDistributor()!= null) ? $orderLine->orderByDistributor()->id() : null
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new OrderLineManagerException("Recovery orderLine error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
    }
    
    /**
     * update
     * 
     * update order line information in the database
     *
     * @param  OrderLine $orderLine
     * @return void
     */
    public function update(OrderLine $orderLine): void
    {
        $request = $this-> db -> prepare("UPDATE orderlines SET item=:item,quantity=:quantity, cart=:cart, orderByDistributor=:orderByDistributor WHERE id = :id");
        $params =array(
            'id'=>$orderLine->id(),
            'item'=> $orderLine->item()->id(),
            'quantity'=> $orderLine->quantity(),
            'cart'=> ($orderLine->cart()!= null) ? $orderLine->cart()->id() : null,
            'orderByDistributor'=> ($orderLine->orderByDistributor()!= null) ? $orderLine->orderByDistributor()->id() : null
        );

        try
        {
            $request ->execute($params);
            
        }
        catch(\PDOException $e)
        {
            $exception= new OrderLineManagerException("Recovery orderLine error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
    }
    
    /**
     * delete
     * 
     * delete OrderLine in database
     *
     * @param  Orderline $orderLine
     * @return void
     */
    public function delete(OrderLine $orderLine): void
    {
        $request = $this-> db -> prepare("DELETE FROM orderlines WHERE id = :id");
        try
        {
            $request ->execute(array(
                'id'=>$orderLine->id()
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new OrderLineManagerException("Recovery orderLine error in the database",700);
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
    }

}


