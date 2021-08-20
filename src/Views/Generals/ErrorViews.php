<?php

namespace App\Views\Generals;

class ErrorViews
{
    public static function error_404(){
        ob_start();
        ?>
        <h1> Page non trouvé </h1>

        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:"404 - " .\DOMAIN_NAME ,content:$content);
    }
}