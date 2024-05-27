<?php

declare(strict_types=1);

namespace App\Entities;

use App\Enums\PlayerEnum;
use App\Exceptions\GameOverException;
use App\Exceptions\InvalidMoveException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Game
{

    private const int BOARD_SIZE = 3;

    private UuidInterface $id;

    /** @var array{array{?int}} */
    private array       $cells      = [[null, null, null], [null, null, null], [null, null, null]];

    private bool        $won        = false;

    private ?PlayerEnum $winner     = null;

    private ?PlayerEnum $lastPlayer = null;

    final public function __construct()
    {
        $this->id = Uuid::uuid7();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function isWon(): bool
    {
        return $this->won;
    }

    public function getWinner(): ?PlayerEnum
    {
        return $this->winner;
    }

    /**
     * @params array<array<?int>> $cells
     */
    private function assertCellsSizeIsCorrect(array $cells): void
    {
        if (self::BOARD_SIZE !== count($cells)) {
            throw new \InvalidArgumentException('The board size is not valid.');
        }

        foreach ($cells as $rows) {
            if (self::BOARD_SIZE !== count($rows)) {
                throw new \InvalidArgumentException('The board size is not valid.');
            }
        }
    }

    /**
     * @param array{array{?int}} $cells
     */
    private function assertCellsAreValid(array $cells): void
    {
        foreach ($cells as $rows) {
            foreach ($rows as $cell) {
                if (!in_array($cell, [null, PlayerEnum::ONE->value, PlayerEnum::TWO->value])) {
                    throw new \InvalidArgumentException('The cells values are not valid');
                }
            }
        }
    }

    public function play(int $row, int $col, PlayerEnum $player): void
    {
        if ($this->won) {
            throw new GameOverException();
        }

        if (max($row, $col) >= self::BOARD_SIZE || (min($row, $col) < 0)) {
            throw new InvalidMoveException('This cell is out of range.');
        }

        if ($this->cells[$row][$col] !== null) {
            throw new InvalidMoveException('This cell is already taken.');
        }

        if ($player === $this->lastPlayer) {
            throw new InvalidMoveException('The same player cannot play two times in a row.');
        }

        $this->cells[$row][$col] = $player->value;
        $this->lastPlayer        = $player;

        $winner = $this->checkWinner();

        if ($winner) {
            $this->winner = $winner;
            $this->won    = true;
        }
    }

    /**
     * @return mixed[]
     */
    public function serialize(): array
    {
        return [
            'id' => $this->getId()->toString(),
            'cells' => $this->cells,
            'isWon' => $this->won,
            'winner' => $this->winner?->value,
            'lastPlayer' => $this->lastPlayer?->value,
        ];
    }

    public static function deserialize(string $value): self
    {
        $data = json_decode($value, true);
        $game = new static();

        $game->id         = Uuid::fromString($data['id']);
        $game->cells      = $data['cells'];
        $game->won        = $data['isWon'];
        $game->winner     = $data['winner'] ? PlayerEnum::from($data['winner']) : null;
        $game->lastPlayer = $data['lastPlayer'] ? PlayerEnum::from($data['lastPlayer']) : null;
        $game->validate();

        return $game;
    }

    private function validate(): void
    {
        $this->assertCellsSizeIsCorrect($this->cells);
        $this->assertCellsAreValid($this->cells);
    }

    private function checkWinner(): ?PlayerEnum
    {
        // Check columns
        for ($row = 0; $row < self::BOARD_SIZE; $row++) {
            $firstCell = $this->cells[$row][0];

            if ($firstCell === null) {
                continue;
            }

            if (($this->cells[$row][1] === $firstCell) && ($this->cells[$row][2] === $firstCell)) {
                return PlayerEnum::from($firstCell);
            }
        }


        // Check rows
        for ($col = 0; $col < self::BOARD_SIZE; $col++) {
            $firstCell = $this->cells[0][$col];

            if ($firstCell === null) {
                continue;
            }

            if (($this->cells[1][$col] === $firstCell) && ($this->cells[2][$col] === $firstCell)) {
                return PlayerEnum::from($firstCell);
            }
        }


        // Check diagonal top-left - bottom-right
        $firstCell = $this->cells[0][0];

        if (($firstCell) !== null && ($this->cells[1][1] === $firstCell) && ($this->cells[2][2] === $firstCell)) {
            return PlayerEnum::from($firstCell);
        }


        // Check diagonal top-right - bottom-left
        $firstCell = $this->cells[0][2];

        if (($firstCell) !== null && ($this->cells[1][1] === $firstCell) && ($this->cells[2][0] === $firstCell)) {
            return PlayerEnum::from($firstCell);
        }

        return null;
    }
}
