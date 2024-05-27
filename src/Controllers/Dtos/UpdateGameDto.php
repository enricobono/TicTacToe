<?php

declare(strict_types=1);

namespace App\Controllers\Dtos;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UpdateGameDto
{

    public function __construct(
        #[Assert\GreaterThanOrEqual(0)]
        #[Assert\LessThanOrEqual(2)]
        public int $row,

        #[Assert\GreaterThanOrEqual(0)]
        #[Assert\LessThanOrEqual(2)]
        public int $col,

        #[Assert\GreaterThanOrEqual(1)]
        #[Assert\LessThanOrEqual(2)]
        public int $player,
    ) {
    }
}
