<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entities;

use App\Entities\Game;
use App\Enums\PlayerEnum;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

class GameTest extends TestCase
{

    public function testCreate(): void
    {
        $game = new Game();

        $this->assertInstanceOf(UuidInterface::class, $game->getId());
        $this->assertFalse($game->isWon());
        $this->assertNull($game->getWinner());
    }

    #[DataProvider('playDataProvider')]
    public function testPlay(int $row, int $col): void
    {
        $game = new Game();

        $game->play($row, $col, PlayerEnum::ONE);

        $this->assertEquals(1, $game->serialize()['cells'][$row][$col]);
    }

    /**
     * @return int[][]
     */
    public static function playDataProvider(): array
    {
        return [
            [0, 0],
            [0, 1],
            [0, 2],
            [1, 0],
            [1, 1],
            [1, 2],
            [2, 0],
            [2, 1],
            [2, 2],
        ];
    }

    public function testCannotPlayACell2Times(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('This cell is already taken.');

        $game = new Game();

        $game->play(0, 0, PlayerEnum::ONE);
        $game->play(0, 0, PlayerEnum::TWO);
    }

    public function testCannotPlay2TimesWithTheSamePlayer(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('The same player cannot play two times in a row.');

        $game = new Game();

        $game->play(0, 0, PlayerEnum::ONE);
        $game->play(0, 1, PlayerEnum::ONE);
    }

    #[DataProvider('cannotPlayACellOutOfTheRangeDataProvider')]
    public function testCannotPlayACellOutOfTheRange(int $row, int $col): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('This cell is out of range');

        $game = new Game();

        $game->play($row, $col, PlayerEnum::ONE);
    }

    /**
     * @return int[][]
     */
    public static function cannotPlayACellOutOfTheRangeDataProvider(): array
    {
        return [
            [4, 3],
            [3, 2],
            [-1, 0],
            [-1, -2],
        ];
    }

    public function testCannotPlayAnEndedGame(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('This game is over.');

        $game = new Game();

        $game->play(0, 0, PlayerEnum::ONE);
        $game->play(1, 0, PlayerEnum::TWO);
        $game->play(0, 1, PlayerEnum::ONE);
        $game->play(1, 1, PlayerEnum::TWO);
        $game->play(0, 2, PlayerEnum::ONE);
        $game->play(0, 2, PlayerEnum::TWO);
    }

    public function testCheckWinnerWhenNobodyWins(): void
    {
        $game = new Game();

        $game->play(0, 0, PlayerEnum::ONE);
        $game->play(0, 1, PlayerEnum::TWO);
        $game->play(0, 2, PlayerEnum::ONE);
        $game->play(1, 0, PlayerEnum::TWO);
        $game->play(1, 2, PlayerEnum::ONE);
        $game->play(1, 1, PlayerEnum::TWO);
        $game->play(2, 0, PlayerEnum::ONE);
        $game->play(2, 2, PlayerEnum::TWO);
        $game->play(2, 1, PlayerEnum::ONE);

        $this->assertFalse($game->isWon());
        $this->assertNull($game->getWinner());
    }

    #[DataProvider('checkWinnerOnHorizontalOrVerticalWinsDataProvider')]
    public function testCheckWinnerOnHorizontalWins(int $player1row, int $player2row): void
    {
        $game = new Game();

        $game->play($player1row, 0, PlayerEnum::ONE);
        $game->play($player2row, 0, PlayerEnum::TWO);
        $game->play($player1row, 1, PlayerEnum::ONE);
        $game->play($player2row, 1, PlayerEnum::TWO);
        $game->play($player1row, 2, PlayerEnum::ONE);

        $this->assertTrue($game->isWon());
        $this->assertEquals(PlayerEnum::ONE, $game->getWinner());
    }

    #[DataProvider('checkWinnerOnHorizontalOrVerticalWinsDataProvider')]
    public function testCheckWinnerOnVerticalWins(int $player1row, int $player2row): void
    {
        $game = new Game();

        $game->play(0, $player1row, PlayerEnum::ONE);
        $game->play(0, $player2row, PlayerEnum::TWO);
        $game->play(1, $player1row, PlayerEnum::ONE);
        $game->play(1, $player2row, PlayerEnum::TWO);
        $game->play(2, $player1row, PlayerEnum::ONE);

        $this->assertTrue($game->isWon());
        $this->assertEquals(PlayerEnum::ONE, $game->getWinner());
    }

    /**
     * @return int[][]
     */
    public static function checkWinnerOnHorizontalOrVerticalWinsDataProvider(): array
    {
        return [
            [0, 2],
            [1, 2],
            [2, 1],
        ];
    }

    public function testCheckWinnerOnDiagonalWins1(): void
    {
        $game = new Game();

        $game->play(0, 0, PlayerEnum::ONE);
        $game->play(0, 1, PlayerEnum::TWO);
        $game->play(1, 1, PlayerEnum::ONE);
        $game->play(1, 0, PlayerEnum::TWO);
        $game->play(2, 2, PlayerEnum::ONE);

        $this->assertTrue($game->isWon());
        $this->assertEquals(PlayerEnum::ONE, $game->getWinner());
    }

    public function testCheckWinnerOnDiagonalWins2(): void
    {
        $game = new Game();

        $game->play(0, 2, PlayerEnum::ONE);
        $game->play(0, 1, PlayerEnum::TWO);
        $game->play(1, 1, PlayerEnum::ONE);
        $game->play(1, 0, PlayerEnum::TWO);
        $game->play(2, 0, PlayerEnum::ONE);

        $this->assertTrue($game->isWon());
        $this->assertEquals(PlayerEnum::ONE, $game->getWinner());
    }
}
