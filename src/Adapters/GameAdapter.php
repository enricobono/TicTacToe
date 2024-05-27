<?php

declare(strict_types=1);

namespace App\Adapters;

use App\Entities\Game;
use App\Enums\PlayerEnum;
use App\Repositories\GameRepository;
use Ramsey\Uuid\UuidInterface;

class GameAdapter
{
    public function __construct(private readonly GameRepository $gameRepository)
    {
    }

    public function create(): Game
    {
        return $this->gameRepository->create();
    }

    public function show(UuidInterface $id): Game
    {
        return $this->gameRepository->get($id);
    }

    public function play(UuidInterface $id, int $row, int $col, PlayerEnum $player): Game
    {
        $game = $this->gameRepository->get($id);

        $game->play($row, $col, $player);

        return $this->gameRepository->save($game);
    }
}
