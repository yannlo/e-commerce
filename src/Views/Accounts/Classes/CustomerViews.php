<?php


namespace App\Views\Accounts\Classes;

use App\Controllers\Tools\Connect;
use App\Domain\Accounts\Classes\Customer;
use App\Views\Generals\Classes\TemplateViews;
use App\Views\Accounts\Interfaces\AccountInterface;
use App\Views\Generals\Interfaces\GeneralViewsInterface;



Class CustomerViews implements AccountInterface, GeneralViewsInterface
{
    public static function index(array $data=[]): void
    {   
        if(Connect::TypeConnectionVerify("customer")){
            $customer =new Customer(Connect::getUser());
        }
        ob_start();
        ?>
        <h1> Bienvenue sur la page abidjan-style </h1>
        <p>
            <a href="/cart">votre panier</a> <br/>
        </p>
        <?php if(isset($customer)):?>
        <p>
            <a href="/order/history">Historique de commande</a> <br/>
        </p>
        <?php endif; ?>
        <p>
            <a href="/item/list">liste des articles</a>
        </p>
        <?php if(Connect::TypeConnectionVerify("customer")):?>
        <p>
            Monsieur <?= $customer->firstName().' '. $customer->lastName() ?> <br/>
            <a href="/logout">deconnexion</a> <br/>
            <a href="/account">information de compte</a>
        </p>
        <?php else:?>
        <p>
            <a href="/signup">Inscrivez-vous</a> <br/>
            <a href="/login">Connectez-vous</a> 

        </p>
        <?php endif ?>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:'accueil | '.\DOMAIN_NAME ,content:$content);
        
    }


    public static function login(array $data=[]): void
    {
        ob_start();
        ?>
        <h1> Connectez vous sur abidjan-style </h1>
        <form action="" method="post">
        <p>
            <?php if(!empty($data)): ?>         
                <p><strong>error:</strong> <?= $data["error"] ?></p>
            <?php endif ?>
            <label for="email">
                Entrer votre email: <input type="email" name="email" id="email">
            </label> <br/>
            <label for="password">
                Entrer votre mot de passe: <input type="password" name="password" id="password">
            </label>
        </p>
        <input type="submit" value="connexion">
        <p>
            <a href="/">Retour a la page d'accueil</a>
        </p>
        </form>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:'Connexion | '.\DOMAIN_NAME  ,content:$content);
    }



    public static function signup(array $data=[]): void
    {
        ob_start();
        ?>
        <h1> Inscrivez vous sur abidjan-style </h1>
        <form action="/signup" method="post">
            <p>
                <label for="firstName">
                    Entrer votre nom: <input type="text" name="firstName" id="firstName">
                </label> <br/>
                <label for="lastName">
                    Entrer votre prenom: <input type="" name="lastName" id="lastName">
                </label> <br/>
                <label for="email">
                    Entrer votre email: <input type="email" name="email" id="email">
                </label> <br/>
                <label for="password">
                    Entrer votre mot de passe: <input type="password" name="password" id="password">
                </label> <br/>
                <label for="confirmation_password">
                    Confirmer votre mot de passe: <input type="password" name="confirmation_password" id="confirmation_password">
                </label>
            </p>
            <input type="submit" value="inscription">
            <p>
                <a href="/">Retour a la page d'accueil</a>
            </p>
        </form>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:'Inscription | '.\DOMAIN_NAME  ,content:$content);
    }



    public static function account(array $data=[]): void
    {
        $customer =$data['customer'];
        ob_start();
        ?>
        <h1>Imformation de compte</h1>
        <p>
            <strong>Identifiant:</strong> <?= $customer->id()?> <br/>
            <strong>Nom:</strong> <?= $customer->firstName()?> <br/>
            <strong>Prenom:</strong> <?= $customer->lastName()?> <br/>
            <strong>email:</strong> <?= $customer->email()?> <br/>
        </p>
        <p>
            <a href="/">Retour a la page d'accueil</a>
        </p>
        <?php
        $content= ob_get_clean();

        TemplateViews::basicTemplate(title:'information de compte | '.\DOMAIN_NAME  ,content:$content);
    }

}