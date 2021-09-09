<?php

namespace App\Models\Orders\Classes;

use App\Domain\Orders\FinalOrder;
use App\Models\Tools\Classes\ConnectDB;
use App\Domain\Orders\OrderByDistributer;

class OrderByDistributerManager
{

    public function __construct(private \PDO $db)
    {
        $this->orderLineManager = new OrderLineManager(ConnectDB::getInstanceToPDO());
    }


    
    // R
    public function getByFinalOrder(FinalOrder $order)
    {
        $request = $this-> db -> prepare('SELECT * FROM ordersbydistributer WHERE `order` = :order');
        $request -> execute(array(
            'order' => $order->id()
        ));

        $ordersByDistributer=[];
        $i= 0;
        while ($data = $request->fetch())
        {
            $data['customer'] = $order->customer();
            $data['order']= $order;
            unset($order->ordersByDistributer()[$i]);
            $orderByDistributer = new OrderByDistributer($data);
            $orderByDistributer->setOrderLines($order->ordersByDistributer()[$i]->orderLines());
            $ordersByDistributer[] = $orderByDistributer;
            $i++;    
        }
        
        return $ordersByDistributer;

    }


    // CUD
    public function add(OrderByDistributer $orderByDistributer): void
    {
        $request = $this->db -> prepare('INSERT INTO ordersbydistributer (distributer,status,customer,`order`) VALUES (:distributer, :status, :customer,:order)');
        try{

            $request->execute(array(
                'distributer' => $orderByDistributer->distributer()->id(),
                'status' => $orderByDistributer->status(),
                'customer' => $orderByDistributer->customer()->id(),
                'order' => $orderByDistributer->order()->id()
            ));
        }
        catch (\PDOException $e)
        {
            dd($e->getMessage());
        }

    }
    public function update(OrderByDistributer $orderByDistributer): void
    {
        $request = $this->db -> prepare('UPDATE ordersByDistributer SET distributer=:distributer, status =:status, customer=:customer WHERE id=:id ');

        try{

            $request->execute(array(
                'id' => $orderByDistributer->id(),
                'distributer' => $orderByDistributer->distributer()->id(),
                'status' => $orderByDistributer->status(),
                'customer' => $orderByDistributer->customer()->id(),
            ));
        }
        catch (\PDOException $e)
        {
            dd($e->getMessage);
        }
    }

    public function delete(OrderByDistributer $orderByDistributer): void
    {
        $request = $this->db -> prepare('DELETE FROM ordersByDistributer WHERE id = :id');
        try{

            $request->execute(array(
                'id' => $orderByDistributer->id()
            ));
        }
        catch (\PDOException $e)
        {
            ($e->getMessage);
        }
    }
    

}