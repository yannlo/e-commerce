<?php

namespace  App\Controllers\Items;

use  App\Views\Items\itemViews;
use App\Models\Items\ItemManager;
use App\Controllers\Tools\Connect;
use App\Domain\Items\Classes\Item;
use  App\Controllers\Tools\URLFormat;
use App\Models\Tools\Classes\ConnectDB;
use App\Models\Accounts\DistributorManager;
use App\Controllers\Accounts\Classes\DistributorController;

class ItemController 
{       
    /**
     * index
     * 
     * print all article list
     *
     * @return void
     */
    public static function index(): void
    {
        $manager = new ItemManager(ConnectDB::getInstanceToPDO());
        $data = $manager->getAll();
        ItemViews::list($data);
    }

    
    /**
     * distributors
     * 
     * print all articles list by distributor
     *
     * @return void
     */
    public static function distributors() :void
    {

    }
    
    /**
     * search
     * 
     * permit to found article in database
     *
     * @return void
     */
    public static function search() :void
    {

    }
    
    /**
     * item
     * 
     * permit to get specific article page
     *
     * @param  int $id
     * @param  string $slug item name in url format
     * @param  null|string $section
     * @return void
     */
    public static function item(int $id, string $slug,?string $section = null): void
    {

        $itemManager = new ItemManager(ConnectDB::getInstanceToPDO());

        $item = $itemManager->getOnce($id);
        $distributor = $item->distributor();

        $trueSlug= URLFormat::slugItemFormat($item); 
        $trueURL= URLFormat::itemFormat($item);

        if($trueSlug !== $slug)
        {
            header('Location: /item/'. $trueURL);
            exit();
        }

        $data = ["item"=>$item, "distributor"=>$distributor];
        ItemViews::item($data);
    }

    
    /**
     * add
     * 
     * add new item in database
     *
     * @return void
     */
    public static function add(): void
    {
        if(!Connect::typeConnectionVerify('distributor'))
        {
            DistributorController::redirectory('login');
        }

        if(!empty($_POST))
        {

            $table = $_POST;
            $table['distributor'] = $_SESSION['distributor']['id'];
            $item = new Item($table);

            $manager = new ItemManager(ConnectDB::getInstanceToPDO());
            self::redirectory('list');
            $manager->add($item);
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

