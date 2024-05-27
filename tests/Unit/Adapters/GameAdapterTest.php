<?php

declare(strict_types=1);

namespace App\Tests\Unit\Adapters;

use App\Adapters\GameAdapter;
use App\Entities\Game;
use App\Enums\PlayerEnum;
use App\Repositories\GameRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GameAdapterTest extends TestCase
{

    private GameAdapter $sut;

    /** @var MockObject<GameRepository> */
    private MockObject $gameRepository;

    public function setUp(): void
    {
        $this->gameRepository = $this->createMock(GameRepository::class);
        $this->sut            = new GameAdapter($this->gameRepository);

        parent::setUp();
    }

    public function testCreate(): void
    {
        $game = new Game();
        $this->gameRepository
            ->expects($this->once())
            ->method('create')
            ->willReturn($game);

        $result = $this->sut->create();

        $this->assertEquals($game, $result);
    }

    public function testShow(): void
    {
        $game = new Game();
        $this->gameRepository
            ->expects($this->once())
            ->method('get')
            ->with($game->getId())
            ->willReturn($game);

        $result = $this->sut->show($game->getId());

        $this->assertEquals($game, $result);
    }

    public function testPlay(): void
    {
        $gameInput = new Game();
        $this->gameRepository
            ->expects($this->once())
            ->method('get')
            ->with($gameInput->getId())
            ->willReturn($gameInput);

        $gameOutput = clone $gameInput;
        $gameOutput->play(0, 1, PlayerEnum::TWO);
        $this->gameRepository
            ->expects($this->once())
            ->method('save')
            ->with($gameOutput)
            ->willReturn($gameOutput);

        $result = $this->sut->play($gameInput->getId(), 0, 1, PlayerEnum::TWO);

        $this->assertInstanceOf(Game::class, $result);
    }
}
