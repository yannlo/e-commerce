<?php


namespace App\Domain\Tools;

use App\Domain\Orders\Cart;
use App\Domain\Orders\OrderLine;
use App\Models\Items\ItemManager;
use App\Domain\Items\Classes\Item;
use App\Models\Tools\Classes\ConnectDB;
use App\Domain\Accounts\Classes\Customer;
use App\Domain\Orders\Exceptions\OrderException;

class JSONFormatter
{

    // item part
    public static function jsonEncoderToItem(item $item)
    {
        $data=[
            'id' =>$item->id()
        ];

        return json_encode($data);
    }


    public static function jsonDecoderToItem(string $json)
    {
        $data = (array)json_decode($json);
  
        return (new ItemManager(ConnectDB::getInstanceToPDO()))->getOnce($data['id']);
    }

    // orderline 
    public static function jsonEncoderToOrderLine(OrderLine $orderLine)
    {
        $data=[
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

    // Cart
    public static function jsonEncoderToCart(Cart $order): string
    {
        $data = array();

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

        $orderLines =[];
        
        if(!empty($data['orderLines']))
        {
            foreach($data['orderLines'] as $orderLine)
            {
                $orderLines[] = self::jsonDecoderToOrderLine($orderLine);
            }
            $data['orderLines'] = $orderLines;
        }

        if(empty($data['orderLines']))
        {
            unset($data['orderLines']);
        }

        $cart = new Cart($data);

        return $cart;
    }
}