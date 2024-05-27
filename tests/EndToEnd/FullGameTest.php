<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FullGameTest extends WebTestCase
{

    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();

        parent::setUp();
    }

    public function testFullGame(): void
    {
        $this->client->request('POST', '/games');
        $id = json_decode($this->client->getResponse()->getContent(), true)['data']['id'];

        $this->client->jsonRequest('PATCH', '/games/' . $id, ['row' => 0, 'col' => 0, 'player' => 1]);
        $this->client->jsonRequest('PATCH', '/games/' . $id, ['row' => 1, 'col' => 0, 'player' => 2]);
        $this->client->jsonRequest('PATCH', '/games/' . $id, ['row' => 0, 'col' => 1, 'player' => 1]);
        $this->client->jsonRequest('PATCH', '/games/' . $id, ['row' => 1, 'col' => 1, 'player' => 2]);
        $this->client->jsonRequest('PATCH', '/games/' . $id, ['row' => 0, 'col' => 2, 'player' => 1]);

        $results = $this->client->getResponse()->getContent();
        $data    = json_decode($results, true)['data'];

        $this->assertResponseIsSuccessful();
        $this->assertJson($results);
        $this->assertEquals($id, $data['id']);
        $this->assertTrue($data['isWon']);
        $this->assertEquals(1, $data['winner']);
        $this->assertEquals(
            [[1, 1, 1], [2, 2, null], [null, null, null]],
            $data['cells']
        );
    }
}
