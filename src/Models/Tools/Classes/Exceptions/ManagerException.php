<?php

namespace App\Models\Tools\Classes\Exceptions;


class ManagerException extends \Exception
{
    protected string $PDOmessage;


    public function getPDOMessage(): string
    {
        return $this-> PDOMessage;
    }

    public function setPDOMessage($message): void
    {
        $this-> PDOMessage = $message;
    }
}