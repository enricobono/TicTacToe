<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controllers;

use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();

        parent::setUp();
    }

    public function testCreate(): void
    {
        $this->client->request('POST', '/games');

        $this->assertResponseStatusCodeSame(201);
        $results = $this->client->getResponse()->getContent();
        $data    = json_decode($results, true)['data'];

        $this->assertJson($results);
        $this->assertTrue(Uuid::isValid($data['id']));
        $this->assertFalse($data['isWon']);
        $this->assertNull($data['winner']);
        $this->assertEquals(
            [[null, null, null], [null, null, null], [null, null, null]],
            $data['cells']
        );
    }

    public function testGet(): void
    {
        $this->client->request('POST', '/games');
        $id = json_decode($this->client->getResponse()->getContent(), true)['data']['id'];

        $this->client->request('GET', '/games/' . $id);

        $results = $this->client->getResponse()->getContent();
        $data    = json_decode($results, true)['data'];

        $this->assertResponseIsSuccessful();
        $this->assertJson($results);
        $this->assertEquals($id, $data['id']);
        $this->assertFalse($data['isWon']);
        $this->assertNull($data['winner']);
        $this->assertEquals(
            [[null, null, null], [null, null, null], [null, null, null]],
            $data['cells']
        );
    }

    public function testGetFailsWhenGameNotFound(): void
    {
        $this->client->request('GET', '/games/00000000-0000-7000-b000-000000000001');

        $results = $this->client->getResponse()->getContent();
        $data    = json_decode($results, true);

        $this->assertResponseStatusCodeSame(404);
        $this->assertJson($results);
        $this->assertEquals('Game not found.', $data['error']);
    }

    public function testGetFailsWhenIdNotValid(): void
    {
        $this->client->request('GET', '/games/123');

        $results = $this->client->getResponse()->getContent();
        $data    = json_decode($results, true);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($results);
        $this->assertEquals('Error while loading this game.', $data['error']);
    }

    public function testUpdate(): void
    {
        $this->client->request('POST', '/games');
        $id = json_decode($this->client->getResponse()->getContent(), true)['data']['id'];

        $data = [
            'row' => 0,
            'col' => 1,
            'player' => 2,
        ];

        $this->client->jsonRequest(
            'PATCH',
            '/games/' . $id,
            $data
        );

        $results = $this->client->getResponse()->getContent();
        $data    = json_decode($results, true)['data'];

        $this->assertResponseIsSuccessful();
        $this->assertJson($results);
        $this->assertEquals($id, $data['id']);
        $this->assertFalse($data['isWon']);
        $this->assertNull($data['winner']);
        $this->assertEquals(
            [[null, 2, null], [null, null, null], [null, null, null]],
            $data['cells']
        );
    }

    public function testUpdateWhenGameNotFound(): void
    {
        $data = [
            'row' => 0,
            'col' => 1,
            'player' => 2,
        ];

        $this->client->jsonRequest(
            'PATCH',
            '/games/00000000-0000-7000-b000-000000000001',
            $data
        );

        $results = $this->client->getResponse()->getContent();
        $data    = json_decode($results, true);

        $this->assertResponseStatusCodeSame(404);
        $this->assertJson($results);
        $this->assertEquals('Game not found.', $data['error']);
    }

    public function testUpdateWhenIdNotValid(): void
    {
        $data = [
            'row' => 0,
            'col' => 1,
            'player' => 2,
        ];

        $this->client->jsonRequest(
            'PATCH',
            '/games/123',
            $data
        );

        $results = $this->client->getResponse()->getContent();
        $data    = json_decode($results, true);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($results);
        $this->assertEquals('Error while playing this move.', $data['error']);
    }

    #[DataProvider('updateFailsOnValidationDataProvider')]
    public function testUpdateFailsOnValidation(string $message, string|int $row, string|int $col, string|int $player): void
    {
        $data = [
            'row' => $row,
            'col' => $col,
            'player' => $player,
        ];

        $this->client->jsonRequest(
            'PATCH',
            '/games/00000000-0000-7000-b000-000000000001',
            $data
        );

        $results = $this->client->getResponse()->getContent();
        $data    = json_decode($results, true);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($results);
        $this->assertEquals($message, $data['error']);
    }

    /**
     * @return array{array{string, string|int, string|int, string|int}}
     */
    public static function updateFailsOnValidationDataProvider(): array
    {
        return [
            ['This value should be less than or equal to 2.', 3, 0, 1],
            ['This value should be greater than or equal to 0.', -1, 0, 1],

            ['This value should be of type int.', 0, 'string', 1],
            ['This value should be less than or equal to 2.', 0, 3, 1],
            ['This value should be greater than or equal to 0.', 0, -1, 1],

            ['This value should be of type int.', 0, 1, 'string'],
            ['This value should be less than or equal to 2.', 0, 0, 3],
            ['This value should be greater than or equal to 1.', 0, 0, 0],
        ];
    }
}
