<?php

namespace App\Views\Orders;

use App\Domain\Orders\Order;
use App\Domain\Tools\NumberFormat;
use App\Views\Generals\Classes\TemplateViews;

class OrderViews
{
    public static function Cart(Order $order)
    {
        $errors=[];
        if(!empty($_SESSION['error']))
        {
            // $errors = $_SESSION['error'];
        }
        $order = $order;
        ob_start();
        ?>
        <h1>Votre panier</h1>
        <?php foreach($errors as $error): ?>
        <p>
            <?= $error ?>
        </p>
        <?php unset($_SESSION['error']); endforeach ?>
        <table>
            <tr>
                <th>Nom de l'article</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>prix</th>
            </tr>
            <?php foreach ($order->orderLines() as $orderLine ):?>
            <?= OrderLineViews::tableFormat($orderLine) ?>
            <?php endforeach ?>
        </table>
        <p>
            <strong>Total:</strong> <?= NumberFormat::priceFormat($order-> getTotalCost()) ?>
        </p>

        <p>
            <a href="/">retour</a>
        </p>

        <?php
        $content= ob_get_clean();
        TemplateViews::basicTemplate(title: "Panier | ".\DOMAIN_NAME ,content:$content);

    }

    public static function history(array $data=[])
    {
        $errors=[];
        if(!empty($_SESSION['error']))
        {
            // $errors = $_SESSION['error'];
        }
        $orders = $data;
        ob_start();
        ?>
        <h1>Votre historique de commande</h1>
        <?php foreach($errors as $error): ?>
        <p>
            <?= $error ?>
        </p>
        <?php unset($_SESSION['error']); endforeach ?>
        <div>
            <?php foreach ($orders as $order):?>
            <div>
                <p>
                    <strong>Numero de commande: </strong> <?= $order-> id()?>
                </p>
                <?php foreach ($order as $orderLine):?>
                <?= OrderLineViews::paragraphFormat($orderLine) ?>
                <?php endforeach ?>
                <p>
                    <strong>Total:</strong> <?= NumberFormat::priceFormat($order-> getTotalCost()) ?>
                </p>
            </div>
            <?php endforeach ?>
        </div>
        <p>
           <a href="/">retour</a>
        </p>
        <?php
        $content= ob_get_clean();
    
        TemplateViews::basicTemplate(title: "Historique de commande | ".\DOMAIN_NAME ,content:$content);
    } 
}