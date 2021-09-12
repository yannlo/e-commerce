<?php

namespace App\Models\Orders\Classes;

use App\Domain\Orders\FinalOrder;
use App\Models\Tools\Classes\ConnectDB;
use App\Domain\Orders\OrderByDistributor;

class OrderByDistributorManager
{

    public function __construct(private \PDO $db)
    {
        $this->orderLineManager = new OrderLineManager(ConnectDB::getInstanceToPDO());
    }


    
    // R
    public function getByFinalOrder(FinalOrder $order)
    {
        $request = $this-> db -> prepare('SELECT * FROM ordersbydistributor WHERE `order` = :order');
        $request -> execute(array(
            'order' => $order->id()
        ));

        $ordersByDistributor=[];
        $i= 0;
        while ($data = $request->fetch())
        {
            $data['customer'] = $order->customer();
            $data['order']= $order;
            unset($order->ordersByDistributor()[$i]);
            $orderByDistributor = new OrderByDistributor($data);
            $orderByDistributor->setOrderLines($order->ordersByDistributor()[$i]->orderLines());
            $ordersByDistributor[] = $orderByDistributor;
            $i++;    
        }
        
        return $ordersByDistributor;

    }


    // CUD
    public function add(OrderByDistributor $orderByDistributor): void
    {
        $request = $this->db -> prepare('INSERT INTO ordersbydistributor (distributor,status,customer,`order`) VALUES (:distributor, :status, :customer,:order)');
        try{

            $request->execute(array(
                'distributor' => $orderByDistributor->distributor()->id(),
                'status' => $orderByDistributor->status(),
                'customer' => $orderByDistributor->customer()->id(),
                'order' => $orderByDistributor->order()->id()
            ));
        }
        catch (\PDOException $e)
        {
            dd($e->getMessage());
        }

    }
    public function update(OrderByDistributor $orderByDistributor,): void
    {
        $request = $this->db -> prepare('UPDATE ordersByDistributor SET distributor=:distributor, status =:status, customer=:customer WHERE id=:id ');

        try{

            $request->execute(array(
                'id' => $orderByDistributor->id(),
                'distributor' => $orderByDistributor->distributor()->id(),
                'status' => $orderByDistributor->status(),
                'customer' => $orderByDistributor->customer()->id()
            ));
        }
        catch (\PDOException $e)
        {
            dd($e->getMessage);
        }
    }

    public function delete(OrderByDistributor $orderByDistributor): void
    {
        $request = $this->db -> prepare('DELETE FROM ordersByDistributor WHERE id = :id');
        try{

            $request->execute(array(
                'id' => $orderByDistributor->id()
            ));
        }
        catch (\PDOException $e)
        {
            ($e->getMessage);
        }
    }
    

}