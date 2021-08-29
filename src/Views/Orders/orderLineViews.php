<?php

namespace App\Views\Orders;

use App\Domain\Orders\OrderLine;
use App\Domain\Tools\NumberFormat;
use App\Controllers\Tools\URLFormat;

class OrderLineViews
{
    public static function tableFormat(OrderLine $orderLine): string
    {
        ob_start();
        ?>
        <tr>
            <td><a href="/item/<?= URLFormat::itemFormat($orderLine->item())?>"><?= $orderLine->item() -> itemName() ?></a></td>
            <td>
                <form action="/cart" method="post">
                    <input type="hidden" name="item"value="<?=$orderLine->item()->id() ?>">
                    <input type="hidden" name="id"value="<?= $orderLine->id() ?>">
                    <input type="number" name="quantity" value="<?= $orderLine->quantity()?>"  min="1" max="<?= $orderLine->item()-> stock() ?>" >
                    <input type="submit" name="submit" value="update">
                </form>
            </td>
            <td><?= NumberFormat::priceFormat($orderLine->item()->price()) ?></td>
            <td><?=  NumberFormat::priceFormat($orderLine->getCost()) ?></td>
            <td><a href="/cart?delete=<?=  $orderLine->id() ?>&item=<?=$orderLine->item()->id() ?>"> supprimer l'article</a></td>

        </tr>
        <?php
        return ob_get_clean();
    }

    public static function paragraphFormat(OrderLine $orderLine): string
    {
        ob_start();
        ?>
        <p>
            <strong>Nom de l'article:</strong> <a href="/item/<?= URLFormat::itemFormat($orderLine->item())?>"><?= $orderLine->item() -> itemName() ?></a> <br />
            <strong>Quantité:</strong> <?= $orderLine->quantity() ?> <br/>
            <strong>Prix unitaire:</strong><?= NumberFormat::priceFormat($orderLine->item()->price()) ?> <br/>
            <strong>Prix:</strong><?= NumberFormat::priceFormat($orderLine->getCost()) ?> <br/>
        </p>
        <?php
        return ob_get_clean();
    }
}