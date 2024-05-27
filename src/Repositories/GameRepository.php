<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Entities\Game;
use App\Exceptions\GameNotFoundException;
use Ramsey\Uuid\UuidInterface;
use Symfony\Contracts\Cache\CacheInterface;

class GameRepository
{

    private CacheInterface $cache;

    public function __construct(
        CacheInterface $cache
    ) {
        $this->cache = $cache;
    }

    public function create(): Game
    {
        $game = new Game();

        $this->cache->get($game->getId()->toString(), function () use ($game): string {
            return (string)json_encode($game->serialize());
        });

        return $game;
    }

    public function get(UuidInterface $id): Game
    {
        $value = $this->cache->get($id->toString(), function (): string {
            throw new GameNotFoundException();
        });

        return Game::deserialize($value);
    }

    public function save(Game $game): Game
    {
        $item = $this->cache->getItem($game->getId()->toString());
        if (!$item->isHit()) {
            throw new GameNotFoundException();
        }

        $item->set((string)json_encode($game->serialize()));
        $this->cache->save($item);

        return $game;
    }
}
