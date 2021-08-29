<?php

namespace App\Models\Orders\Classes;

use App\Domain\Orders\Order;
use App\Domain\Orders\OrderLine;
use App\Models\Items\ItemManager;
use App\Models\Tools\Classes\ConnectDB;
use App\Models\Orders\Classes\Exceptions\OrderLineManagerException;



class OrderLineManager
{
    private ItemManager $itemManager;
    
    public function __construct(private \PDO $db)
    {
        $this->itemManager = new ItemManager(ConnectDB::getInstanceToPDO());
    }

    public function getByOrder(Order $order)
    {
        $request = $this-> db -> prepare("SELECT * FROM orderlines  WHERE `order`= :order");

        try
        {
            $request ->execute(array(
                "order" => $order->id()
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new OrderLineManagerException("Recovery orderLine error in the database");
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }

        if($request->rowCount()===0)
        {
            $exception= new OrderLineManagerException("Never orderLine to this order");
            throw $exception;
            return;
        }

        $table=[];
        while($data = $request->fetch())
        {

            $data["item"] = $this->itemManager->getOnce($data['item']);
            $data["order"] = $order;

            $table[] = new OrderLine($data);
        }

        return $table;
    }

    public function add(OrderLine $orderLine): void
    {
        $request = $this-> db -> prepare("INSERT INTO orderlines (item,quantity,`order`) VALUES(:item,:quantity,:order)");
        try
        {
            $request ->execute(array(
                'item'=> $orderLine->item()->id(),
                'quantity'=> $orderLine->quantity(),
                'order'=> $orderLine->order()->id()
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new OrderLineManagerException("Recovery orderLine error in the database");
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
    }

    public function update(OrderLine $orderLine): void
    {
        $request = $this-> db -> prepare("UPDATE orderlines SET item=:item,quantity=:quantity,`order`=:order WHERE id = :id");
        try
        {
            $request ->execute(array(
                'id'=>$orderLine->id(),
                'item'=> $orderLine->item()->id(),
                'quantity'=> $orderLine->quantity(),
                'order'=> $orderLine->Order()->id()
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new OrderLineManagerException("Recovery orderLine error in the database");
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
    }

    public function delete(OrderLine $orderLine): void
    {
        $request = $this-> db -> prepare("SELECT FROM orderlines WHERE id = :id");
        try
        {
            $request ->execute(array(
                'id'=>$orderLine->id()
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new OrderLineManagerException("Recovery orderLine error in the database");
            $exception->setPDOMessage($e->getMessage());
            throw $exception;
            return;
        }
    }

    public function ifOrderLineExists(OrderLine $orderLine): bool|null
    {
        $request = $this-> db -> prepare("SELECT * FROM orderlines  WHERE id= :id");

        try
        {
            $request ->execute(array(
                "id" => $orderLine->id()
            ));
            
        }
        catch(\PDOException $e)
        {
            $exception= new OrderLineManagerException("Recovery orderLine error in the database");
            $exception->setPDOMessage($e->getMessage());
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

