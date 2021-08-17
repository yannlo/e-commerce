<?php

namespace App\Views\Account\Classes;

use App\Views\Account\Interfaces\AccountInterface;
use App\Views\Generals\TemplateViews;

class DistributerViews implements AccountInterface
{
    public static function index(array $data=[]): void
    {
        ob_start();
        ?>
        <p> Bienvenue sur la page distributeur abidjan-style </p>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:\HEAD_NAME ,content:$content);
    }
    public static function login(array $data=[]): void
    {
        ob_start();
        ?>
        <p> Connectez vous en tant que distributeur sur abidjan-style </p>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:\HEAD_NAME ,content:$content);
    }

    public static function signup(array $data=[]): void
    {
        ob_start();
        ?>
        <p> inscrivez vous et devenez distributeur sur abidjan-style </p>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:\HEAD_NAME ,content:$content);
    }

    public static function account(array $data=[]): void
    {
        ob_start();
        ?>
        <p>Imformation de compte distributeur </p>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:\HEAD_NAME ,content:$content);
    }
}
