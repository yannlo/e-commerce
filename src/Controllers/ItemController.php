<?php

namespace  App\Controllers;

use  App\Views\Items\itemViews;
use App\Models\Items\ItemManager;
use App\Domain\Items\Classes\Item;
use  App\Controllers\Tools\URLFormat;
use App\Models\Tools\Classes\ConnectDB;
use App\Models\Accounts\DistributerManager;
use  App\Controllers\Account\Classes\DistributerController;

class ItemController 
{   
    public static function list(): void
    {
        $manager = new ItemManager(ConnectDB::getInstanceToPDO());
        $data = $manager->getAll();
        ItemViews::list($data);
    }

    public static function item(int $id, string $slug): void
    {
        $itemManager = new ItemManager(ConnectDB::getInstanceToPDO());
        $distributerManager = new DistributerManager(ConnectDB::getInstanceToPDO());
        $item = $itemManager->getOnce($id);
        $distributer = $distributerManager->getOnce($item->idDistrib());
        $trueSlug= URLFormat::slugItemFormat($item); 
        $trueURL= URLFormat::itemFormat($item); 
        if($trueSlug !== $slug)
        {
            header('Location: /item/'. $trueURL);
            exit();
        }
        $data = ["item"=>$item, "distributer"=>$distributer];
        ItemViews::item($data);
    }

    public static function add(): void
    {
        if(DistributerController::is_connected('distributer')===false)
        {
            DistributerController::redirectory('login');
        }

        if(!empty($_POST))
        {

            $table = $_POST;
            $table['idDistrib'] = $_SESSION['distributer']['id'];
            $item = new Item($table);

            $manager = new ItemManager(ConnectDB::getInstanceToPDO());
            $manager->add($item);

            self::redirectory('list');
            return;
        }

        ItemViews::add();
    }

    public static function delete(): void
    {
        if(empty($_GET))
        {
            self::redirectory('list');
        }
        
        $item = new Item($_GET);

        $manager = new ItemManager(ConnectDB::getInstanceToPDO());
        $manager->delete($item);

        self::redirectory('list');

    }

    private static function redirectory(string $page) 
    {
        header('Location: /item/'.$page);
        exit();
    }
}

