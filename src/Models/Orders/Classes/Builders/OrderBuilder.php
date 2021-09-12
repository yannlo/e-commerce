<?php

namespace App\Models\Orders\Classes\Builders;

use App\Domain\Orders\Order;
use App\Domain\Accounts\Classes\Customer;
use App\Models\Orders\Classes\OrderManager;
use App\Models\Orders\Classes\Builders\OrderByDistributorBuider;
use App\Models\Orders\Classes\Exceptions\OrderByDistributorManagerException;

class OrderBuider
{
    private OrderManager $orderManager;
    private OrderByDistributorBuider $orderByDistributorBuider;

    public function __construct(private \PDO $db)
    {
        $this->orderManager = new OrderManager($this->db);
        $this->orderByDistributorBuider = new OrderByDistributorBuider($this->db);
    }

    // CRU
    // get    
    /**
     * getOnce
     * 
     * get complet order
     *
     * @param  int $id
     * @return order
     */
    public function getOnce(int $id):?order
    {
        $order = $this ->orderManager-> getOnce($id);

        $ordersByDistributor = $this->orderByDistributorBuider->getByOrder($order);

        $order->setOrdersByDistributor($ordersByDistributor);
        
        return $order;

    }
    
    /**
     * getAllByCustomer
     * 
     * get complet order in array by customer
     *
     * @param  Customer $customer
     * @return array
     */
    public function getAllByCustomer(Customer $customer): ?array
    {
        $orders = $this -> orderManager -> getAllByCustomer($customer);

        foreach($orders as $order)
        {
            $ordersByDistributor = $this->orderByDistributorBuider->getByOrder($order);

            $order->setOrdersByDistributor($ordersByDistributor);
        }

        return $orders;
    }
        
    /**
     * getLastByCustomer
     * 
     * get last order completed by customer
     *
     * @param  Customer $customer
     * @return Order
     */
    public function getLastByCustomer(Customer $customer): ?Order
    {
        $order = $this -> orderManager -> getLastByCustomer($customer);

        $ordersByDistributor = $this->orderByDistributorBuider->getByOrder($order);

        $order->setOrdersByDistributor($ordersByDistributor);

        return $order;

    }
    
    /**
     * getByStatusIAndCustomer
     * 
     * get complet order filter by status
     *
     * @param  Customer $customer
     * @param  int $status
     * @return array
     */
    public function getByStatusIAndCustomer(Customer $customer,int $status): ?array
    {
        $orders = $this -> orderManager -> getByStatusAndCustomer($customer, $status);

        foreach($orders as $order)
        {
            $ordersByDistributor = $this->orderByDistributorBuider->getByOrder($order);

            $order->setOrdersByDistributor($ordersByDistributor);
        }

        return $orders;

    }
    
    // Add    
    /**
     * add
     * 
     * permit to add complet order in database
     *
     * @param  Order $order
     * @return void
     */
    public function add(Order $order): void
    {
        $this -> orderManager ->add($order);

        foreach ($order->ordersByDistributor()  as $orderByDistributor)
        {
            $this ->orderByDistributorBuider -> add($orderByDistributor);
        }
    }
    
    // Update    
    /**
     * update
     * 
     * permit to update complet order in database
     *
     * @param  Order $order
     * @return void
     */
    public function update(Order $order): void
    {
        $this -> orderManager ->update($order);

        foreach ($order->ordersByDistributor()  as $orderByDistributor)
        {
            $this ->orderByDistributorBuider -> update($orderByDistributor);
        }
    }


}