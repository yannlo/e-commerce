<?php


namespace App\Domain\Tools;

use App\Domain\Orders\Order;
use App\Domain\Orders\OrderLine;
use App\Domain\Items\Classes\Item;
use App\Domain\Accounts\Classes\Customer;
use App\Domain\Orders\Exceptions\OrderException;

class JSONFormatter
{

    // item part
    public static function jsonEncoderToItem(item $item)
    {
        $data=[
            'id' =>$item->id(),
            'itemName'=>$item->itemName(),
            'stock' =>$item->stock(),
            'price' =>$item-> price(),
            'idDistrib' =>$item->idDistrib()
        ];

        return json_encode($data,JSON_FORCE_OBJECT);
    }


    public static function jsonDecoderToItem(string $json)
    {
        $data = (array)json_decode($json);
        return new Item($data);
    }


    // customer
    public static function jsonEncoderToCustomer(Customer $customer)
    {

        $data=[
            'id' =>$customer->id(),
            'firstName'=>$customer->firstName(),
            'lastName' =>$customer->lastName(),
            'email' =>$customer-> email(),
            'password' =>$customer->password()
        ];

        return json_encode($data, JSON_FORCE_OBJECT);
    }


    public static function jsonDecoderToCustomer(string $json)
    {
        $data = (array)json_decode($json);
        return new Customer($data);
    }

    // orderline 
    public static function jsonEncoderToOrderLine(OrderLine $orderLine)
    {
        $data=[
            'id' =>$orderLine->id(),
            'item' =>self::jsonEncoderToItem($orderLine->item()),
            'quantity' =>$orderLine-> quantity(),
        ];

        return json_encode($data,JSON_FORCE_OBJECT);
    }

    public static function jsonDecoderToOrderLine(string $json)
    {
        $data = (array)json_decode($json);
        $data['item'] = self::jsonDecoderToItem($data['item']);
        return new OrderLine($data);
    }

    // order
    public static function jsonEncoderToOrder(Order $order): string
    {
        $data = array(
            "id"=>$order->id(),
            "status"=>$order->status()
        );
        if($order->customer()->id() !== 0)
        {
            $data["customer"]=self::jsonEncoderToCustomer($order->customer());
        }
        $orderLinesJson=[];
        foreach($order->orderLines()  as $orderLine)
        {
            $orderLinesJson[]=self::jsonEncoderToOrderLine($orderLine);
        }
        $data['orderLines']=$orderLinesJson;
        return json_encode($data,JSON_FORCE_OBJECT);
    }

    public static function jsonDecoderToOrder(string $json)
    {
        $data = (array) json_decode($json);  
        if(isset($data['customer']))
        {
            $data['customer'] = self::jsonDecoderToCustomer($data['customer']);
        }
        $orderLines =[];

        foreach($data['orderLines'] as $orderLine)
        {
            $orderLines[] = self::jsonDecoderToOrderLine($orderLine);
        }

        $data['orderLines'] = $orderLines;

        try
        {
            $order = new Order($data);
        }
        catch (OrderException $e)
        {
            if($e->getCode()===200)
            {
                unset($data['orderLines']);
            }
            $order = new Order($data);

        }
        return $order;
    }
}