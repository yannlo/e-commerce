<?php

namespace App\Views\Accounts\Classes;

use App\Views\Items\ItemViews;
use App\Views\Generals\Classes\TemplateViews;
use App\Views\Accounts\Interfaces\AccountInterface;
use App\Views\Generals\Interfaces\GeneralViewsInterface;




class DistributorViews implements AccountInterface, GeneralViewsInterface
{
    public static function index(array $data=[]): void
    {
        ob_start();
        ?>
        <h1> Bienvenue sur la page distributeur abidjan-style </h1>
        <p>
            <a href="/items">liste des article</a> <br/>
            <a href="/account">Informaion de compte</a> <br/>
            <a href="/logout">Deconnexion</a>
        </p>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:"Accueil | ".\DOMAIN_NAME ,content:$content);
    }



    public static function login(array $data=[]): void
    {
        ob_start();
        ?>
        <h1> Connectez vous en tant que distributeur sur abidjan-style </h1>
        <form action="/login" method="post">
            <?php if(!empty($data)): ?>         
                <p><strong> <?= $data["error"] ?> error:</strong> <?= $data["message"] ?></p>
            <?php endif ?>
            <p>
                <label for="email">
                    Entrer votre email: <input type="email" name="email" id="email">
                </label> <br/>
                <label for="password">
                    Entrer votre mot de passe: <input type="password" name="password" id="password">
                </label>
                <input type="submit" value="connexion">
            </p>
        </form>
        <p>
            <a href="/signup">S'inscrire</a>
        </p>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:"Connexion | ".\DOMAIN_NAME ,content:$content);
    }




    public static function signup(array $data=[]): void
    {
        
        ob_start();
        ?>
        <h1> inscrivez vous et devenez distributeur sur abidjan-style </h1>
        <form action="/signup" method="post">
            <p>
                <label for="nameDistrib">
                    Entrer le nom de votre marque: <input type="text" name="nameDistrib" id="nameDistrib">
                </label> <br/>

                <label for="email">
                    Entrer votre email: <input type="email" name="email" id="email">
                </label> <br/>
                <label for="password">
                    Entrer votre mot de passe: <input type="password" name="password" id="password">
                </label> <br/>
                <label for="confirmation_password">
                    Confirmer votre mot de passe: <input type="password" name="confirmation_password" id="confirmation_password">
                </label> <br/>
                <label for="description">Decrivez votre entreprise ou marque: </label><br/>
                <textarea name="description" id="description" cols="30" rows="10"></textarea>
            </p>
            <input type="submit" value="inscription">
            <p>
                <a href="/login">Se connecter</a>
            </p>
        </form>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:"Inscription | ".\DOMAIN_NAME ,content:$content);
    }



    
    public static function account(array $data=[]): void
    {
        $distributor =$data['distributor'];
        ob_start();
        ?>
        <h1>Information de compte distributeur</h1>
        <p>
            <strong>Identifiant:</strong> <?= $distributor->id()?> <br/>
            <strong>Nom:</strong> <?= $distributor->nameDistrib()?> <br/>
            <strong>email:</strong> <?= $distributor->email()?> <br/>
            <strong>Description:</strong> <?= $distributor->description()?> <br/>
        </p>
        <p>
            <a href="/">Retour a la page d'accueil</a>
        </p>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title: "information de compte | ".\DOMAIN_NAME ,content:$content);
    }

    public static function items(array $data=[]): void
    {
        $items = $data['items'];
        ob_start();
        ?>
        <h1>Liste des items vendu</h1>
        <div>
            <a href="/item/add">Ajouter un article</a>

        <?php foreach ($items as $item):?>
            <?= ItemViews::ListFormat($item) ?>
        <?php endforeach ?>

        </div>
        <?php
        $content = ob_get_clean();

        TemplateViews::basicTemplate(title: "listes des articles | ".\DOMAIN_NAME ,content:$content);

    }
}
