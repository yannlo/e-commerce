<?php


namespace App\Domain\Orders;

use App\Domain\Orders\Order;
use App\Domain\Orders\OrderLine;
use App\Models\Tools\Classes\ConnectDB;
use App\Models\Accounts\DistributerManager;
use App\Domain\Accounts\Classes\Distributer;
use App\Domain\Payment\Interfaces\PaymentInterface;
use App\Domain\Delivery\Interfaces\DeliveryInterface;
use App\Domain\Orders\Exceptions\OrderByDistributerException;

class OrderByDistributer extends Order
{
    private Distributer $distributer;
    private DeliveryInterface $deliveryMethod;
    private PaymentInterface $paymentMethod;

    private int $status = self::BEING_PROCESSED;

    public function addOrderLine(OrderLine $orderLine): void
    {

        if (empty($orderLines))
        {
            parent::addOrderLine($orderLine);
            $distributerManager = new DistributerManager(ConnectDB::getInstanceToPDO());
            $this->distributer= $distributerManager ->getOnce($this->orderLines[0]->item()->idDistrib());
            return;
        }

        if($orderLine->item()->idDistrib()!== $this->distributer->id())
        {
            throw new OrderByDistributerException('item distributer is diferent');
            return;
        }

        parent::addOrderLine($orderLine);
        
    }

    public function getCost()
    {
        // recupere le prix de la livraison

        // ...


        parent::getTotalCost();
    }


    public function distributer(): Distributer
    {
        return $this->distributer;
    }


}