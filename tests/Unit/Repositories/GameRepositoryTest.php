<?php

declare(strict_types=1);

namespace App\Tests\Unit\Repositories;

use App\Entities\Game;
use App\Exceptions\GameNotFoundException;
use App\Repositories\GameRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class GameRepositoryTest extends TestCase
{

    private GameRepository $sut;

    /** @var MockObject<FilesystemAdapter> */
    private MockObject $cache;

    public function setUp(): void
    {
        $this->cache = $this->createMock(FilesystemAdapter::class);
        $this->sut   = new GameRepository($this->cache);

        parent::setUp();
    }

    public function testCreate(): void
    {
        $this->cache
            ->expects($this->once())
            ->method('get');

        $result = $this->sut->create();

        $this->assertInstanceOf(Game::class, $result);
    }

    public function testGet(): void
    {
        $game = new Game();
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with($game->getId()->toString())
            ->willReturn(json_encode($game->serialize()));

        $result = $this->sut->get($game->getId());

        $this->assertInstanceOf(Game::class, $result);
    }

    public function testGetWhenItemNotFound(): void
    {
        $this->expectException(GameNotFoundException::class);
        $this->expectExceptionMessage('Game not found.');

        $game = new Game();
        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with($game->getId()->toString())
            ->willThrowException(new GameNotFoundException());

        $this->sut->get($game->getId());
    }

    public function testSaveWhenItemNotFound(): void
    {
        $this->expectException(GameNotFoundException::class);
        $this->expectExceptionMessage('Game not found.');

        $game = new Game();

        $this->cache
            ->expects($this->once())
            ->method('getItem')
            ->with($game->getId()->toString())
            ->willThrowException(new GameNotFoundException());

        $this->sut->save($game);
    }
}
