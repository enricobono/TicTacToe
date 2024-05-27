<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Adapters\GameAdapter;
use App\Controllers\Dtos\UpdateGameDto;
use App\Enums\PlayerEnum;
use App\Exceptions\GameNotFoundException;
use App\Exceptions\InvalidMoveException;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class GamesController extends AbstractController
{

    public function __construct(readonly private GameAdapter $gameAdapter)
    {
    }

    #[Route('/games', name: 'game.create', methods: ['POST'], format: 'json')]
    public function create(): JsonResponse
    {
        $game = $this->gameAdapter->create();

        return $this->json(
            ['data' => $game->serialize()],
            Response::HTTP_CREATED
        );
    }

    #[Route('/games/{id}', name: 'games.update', methods: ['PATCH'], format: 'json')]
    public function update(
        string $id,
        #[MapRequestPayload(validationFailedStatusCode: 400)] UpdateGameDto $request
    ): JsonResponse {
        try {
            $player = PlayerEnum::from($request->player);

            $data = $this->gameAdapter->play(
                Uuid::fromString($id), $request->row, $request->col, $player
            );

            return $this->json(['data' => $data->serialize()]);
        } catch (GameNotFoundException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_NOT_FOUND,
            );
        } catch (InvalidMoveException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_BAD_REQUEST,
            );
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Error while playing this move.'],
                Response::HTTP_BAD_REQUEST,
            );
        }
    }

    #[Route('/games/{id}', name: 'games.view', format: 'json')]
    public function get(string $id): JsonResponse
    {
        try {
            $data = $this->gameAdapter->show(Uuid::fromString($id));

            return $this->json(['data' => $data->serialize()]);
        } catch (GameNotFoundException $e) {
            return $this->json(
                ['error' => $e->getMessage()],
                Response::HTTP_NOT_FOUND,
            );
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Error while loading this game.'],
                Response::HTTP_BAD_REQUEST,
            );
        }
    }
}
