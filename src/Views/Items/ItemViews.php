<?php

namespace App\Views\Items;

use App\Domain\Tools\NumberFormat;
use App\Controllers\Tools\URLFormat;
use App\Views\Generals\Classes\TemplateViews;


class itemViews
{
    public static function item(array $data=[]): void
    {
        $item = $data['item'];
        $distributer = $data['distributer'];

        ob_start();
        ?>
        <h1> Bienvenue sur la page abidjan-style </h1>
        <div>
            <h2>Detail de l'article</h2>
            <p>
                <strong>Identifiant de l'article:</strong> <?= $item-> id() ?> <br/>
                <strong>Nom de l'article:</strong> <?= $item-> itemName() ?> <br/>
                <strong>Prix de l'article:</strong> <?= NumberFormat::priceFormat($item->price()) ?> <br/>
                <strong>Nom du distributer:</strong> <?= $distributer-> nameDistrib() ?>
            </p>

            <?php if(empty($_SESSION['distributer']) || !isset($_SESSION['distributer'])):?>
                <h2>Commander un article</h2>
            <form action="/cart" method="post">
                <p>
                    <label for="quantity">Quanité :<input type="number" min="1" max="<?= $item-> stock() ?>" name="quantity" id="quantity"></label><br/>
                    <input type="hidden" name="item" value="<?= $item-> id() ?>">
                    <input type="submit" name="submit" value="Ajouter au panier">
                </p>
            </form>
            <?php endif ?>

            <?php if(!empty($_SESSION['distributer'])&&isset($_SESSION['distributer'])):?>
            <p>
                <a href="/item/delete?id=<?= $item-> id()?>">suprimerl'aritcle</a>
            </p>
            <?php endif ?>

            <p>
                <a href="/item/list">retour</a>
            </p>
        </div>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title: $item->itemName()." | ".\DOMAIN_NAME ,content:$content);
    }

    public static function list(array $data=[]): void
    {

        ob_start();
        ?>
        <h1> Bienvenue sur la page abidjan-style </h1>
        <div>
            <h2>Liste des articles</h2>

            <?php if(!empty($_SESSION['distributer']) && isset($_SESSION['distributer'])):?>
            <p>
                <a href="/item/add">Ajouter un article</a>
            </p>
            <?php endif ?>

            <?php if($data===[]): ?>
            <p>
                Aucun article enregistrer sur le site
            </p>
            <?php else: ?>
                <?php foreach($data as $item): ?>
            <p>
                <strong>Nom de l'article:</strong> <?= $item-> itemName() ?> <br/>
                <strong>Prix de l'article:</strong> <?= NumberFormat::priceFormat($item->price()) ?> <br/>
                <a href="/item/<?= URLFormat::itemFormat($item)?>">detail >></a>
            </p>
                <?php endforeach ?>
            <?php endif ?>
            <p>
                <a href="/">retour</a>
            </p>
        </div>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title: "Liste des articles | ".\DOMAIN_NAME ,content:$content);
    }

    public static function add(): void
    {

        ob_start();
        ?>
        <h1> Bienvenue sur la page abidjan-style </h1>
        <div>
            <h2>Ajouter des articles</h2>

            <form action="/item/add" method="post">
                <p>
                    <label for="itemName">Nom de l'article: <input type="text" id="itemName" name='itemName'></label><br/>
                    <label for="price">Prix de l'article<input type="number" min="1000" max="100000" id="price" name="price"></label><br/>
                    <label for="stock">Stock de l'article<input type="number" min="1" max="1000" id="stock" name="stock"></label>
                </p>
                <input type="submit" value="Ajouter">
            </form>
            <p>
                <a href="/item/list">retour</a>
            </p>
        </div>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title: "Ajouter un article | ".\DOMAIN_NAME ,content:$content);
    }
}