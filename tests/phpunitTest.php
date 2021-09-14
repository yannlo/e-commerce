<?php


use PHPUnit\Framework\TestCase;
use App\Controllers\Tools\Connect;

class phpunitTest extends TestCase
{
    public function testTrue()
    {
        Connect::getUser();
        dd(Connect::getUser());
    }
}