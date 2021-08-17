<?php

namespace App\Views;
use App\Views\Generals\TemplateViews;

class itemViews
{
    public static function item(array $data=[]): void
    {

        ob_start();
        ?>
        <h1> Bienvenue sur la page abidjan-style </h1>
        <div>
            <p>
                <strong>Nom de l'article:</strong> <?= $data['item']-> itemName() ?> <br/>
                <strong>Prix de l'article:</strong> <?= $data['item']-> price() ?> <br/>
            </p>
        </div>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title: \HEAD_NAME ,content:$content);
    }
}