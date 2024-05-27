<?php

declare(strict_types=1);

namespace App\Exceptions;

class GameNotFoundException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Game not found.');
    }
}
