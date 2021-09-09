<?php

namespace App\Domain\Orders;

use App\Domain\Orders\Exceptions\OrderByDistributerException;
use App\Domain\Factory\PaymentFactory\Interfaces\PaymentFactoryInterface;

class FinalOrder extends Order
{

    //variables
    private array $ordersByDistributer = [];
    private PaymentFactoryInterface $paymentMethod;


    // construct
    public function __construct(array $data)
    {

        parent::__construct($data);

        // cas 1: post
        if (empty($this->ordersByDistributer))
        {
            $this->OrderLineByItemDistributer($data);
        }
        unset($this->orderLines);

        $this->updateState();
    }

    // metthod
    public function pay()
    {
        $this->paymentMethod -> pay($this->getTotalCost());
    }
    
    public function OrderLineByItemDistributer(array $data)
    {
        $data["order"]= $this;
        unset($data['orderLines']);
        
        foreach($this->orderLines as $orderLine)
        {
            if(empty($this->ordersByDistributer))
            {
                $this-> addOrderByDistributer($data);
                end($this-> ordersByDistributer)->addOrderLine($orderLine);
                continue;
            }

            
            $is_include= false;
            foreach($this->ordersByDistributer as  $orderByDistributer)
            {                
                try
                {
                    $orderByDistributer->addOrderLine($orderLine);
                    $is_include= true;
                    break;
                }
                catch(OrderByDistributerException $e)
                {
                    continue;
                }

            }

            if(!$is_include)
            {
                $this-> addOrderByDistributer($data);
                end($this-> ordersByDistributer)->addOrderLine($orderLine);
            }



        }
    }

    private function addOrderByDistributer($data)
    {
        
        $orderByDistributer = new OrderByDistributer($data);
        $this-> ordersByDistributer[] = $orderByDistributer;
    }

    public function updateState()
    {
        $stateList = array();
        foreach($this->ordersByDistributer as $orderByDistributer)
        {
            $stateList[]= $orderByDistributer->status();
        }

        $this->setStatus(min($stateList));
    }

    public function setOrdersByDistributer(array $ordersByDistributer)
    {

        foreach($ordersByDistributer as $orderByDistributer)
        {
            if(!is_a($orderByDistributer,get_class($orderByDistributer)))
            {
                throw new OrderByDistributerException('incorrect value in array');
                return;
            }
        }
        
        $this->ordersByDistributer = $ordersByDistributer;
    }

    public function getTotalCost(): int
    {
        $cost=0;
        foreach($this->ordersByDistributer as $orderByDistribut)
        {
            $cost+=$orderByDistribut->getTotalCost();
        }
        return $cost;
    }

    public function ordersByDistributer():array
    {
        return $this->ordersByDistributer;
    }
}