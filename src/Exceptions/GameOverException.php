<?php

declare(strict_types=1);

namespace App\Exceptions;

class GameOverException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('This game is over.');
    }
}
