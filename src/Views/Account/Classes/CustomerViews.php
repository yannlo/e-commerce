<?php


namespace App\Views\Account\Classes;

use App\Views\Account\Interfaces\AccountInterface;
use App\Views\Generals\TemplateViews;

Class CustomerViews implements AccountInterface
{
    public static function index(array $data=[]): void
    {   
        ob_start();
        ?>
        <p> Bienvenue sur la page abidjan-style </p>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:\HEAD_NAME ,content:$content);
        
    }
    public static function login(array $data=[]): void
    {
        ob_start();
        ?>
        <p> Connectez vous sur abidjan-style </p>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:\HEAD_NAME ,content:$content);
    }

    public static function signup(array $data=[]): void
    {
        ob_start();
        ?>
        <p> inscrivez vous et commander vos articles sur abidjan-style </p>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:\HEAD_NAME ,content:$content);
    }

    public static function account(array $data=[]): void
    {
        ob_start();
        ?>
        <p>Imformation de compte</p>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:\HEAD_NAME ,content:$content);
    }

}