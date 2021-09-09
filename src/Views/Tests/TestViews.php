<?php

namespace App\Views\Tests;

class Test
{
    public static function index()
    {

            ob_start();
            ?>
            
            <?php
            return ob_get_clean();  
    }
}