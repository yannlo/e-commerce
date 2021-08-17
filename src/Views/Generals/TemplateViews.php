<?php

namespace App\Views\Generals;

Class TemplateViews
{
    public static function basicTemplate(string $title="", string $content=""): void
    {
        ?>
        <!DOCTYPE html>
        <html lang="fr">

        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $title ?></title>
        </head>

        <body>
            <?= $content ?>
        </body>

        </html>
        <?php
    }
}