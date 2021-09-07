<?php

namespace App\Domain\Orders;

use App\Domain\Orders\Exceptions\OrderByDistributerException;

class FinalOrder extends Order
{
    private int $status = self::BEING_PROCESSED;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->OrderLineByItemDistributer($data);
        $this->updateState();
    }

    private array $ordersByDistributer=[];

    public function OrderLineByItemDistributer(array $data)
    {
        foreach($this->orderLines as $orderLine)
        {
            if(empty($this->ordersByDistributer))
            {
                $this-> addOrderByDistributer($data);
                end($this-> ordersByDistributer)->addOrderLine($orderLine);
                continue;
            }

            
            $is_include= false;
            foreach($this->ordersByDistributer as $orderByDistributer)
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
            $stateList[]= $orderByDistributer->status;
        }

        $this->setStatus(min($stateList));
    }
}