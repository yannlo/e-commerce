<?php

namespace App\Views\Generals\Classes;

class ErrorViews
{
    public static function error_404($page){
        ob_start();
        ?>
        <h1> Page non trouvé </h1>
        <?= $page; ?>
        <?php dump($_SERVER)?>
        <?php dump($_GET)?>
        <?php dump($_POST)?>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:"404 | " .\DOMAIN_NAME ,content:$content);
    }
}