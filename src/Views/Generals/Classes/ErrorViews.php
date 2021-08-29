<?php

namespace App\Views\Generals\Classes;

class ErrorViews
{
    public static function error_404($page){
        ob_start();
        ?>
        <h1> Page non trouvé </h1>
        <h2> <?= $page; ?> </h2>
        <h2>Variable  Serveur</h2>
        <?php dump($_SERVER)?>
        <h2> Variable get </h2>
        <?php dump($_GET)?>
        <h2> Variable Post </h2>
        <?php dump($_POST)?>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:"404 | " .\DOMAIN_NAME ,content:$content);
    }
}