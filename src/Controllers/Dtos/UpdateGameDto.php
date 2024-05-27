<?php

declare(strict_types=1);

namespace App\Controllers\Dtos;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateGameDto
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(0)]
        #[Assert\LessThanOrEqual(2)]
        public readonly int $row,

        #[Assert\GreaterThanOrEqual(0)]
        #[Assert\LessThanOrEqual(2)]
        public readonly int $col,

        #[Assert\GreaterThanOrEqual(1)]
        #[Assert\LessThanOrEqual(2)]
        public readonly int $player,
    ) {
    }
}
