<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Api\Common\LoginTrait;
use App\Tests\Common\CommentTrait;

class NoteTest extends ApiTestCase
{
    use CommentTrait;
    use LoginTrait;

    public function testMemberShouldNotBeAbleToRateOwnComment(): void
    {
        $client = static::createClient();
        $token  = $this->getToken($client, 'Alice', 'alice');

        $note = [
            'rate' => 5,
            'comment' => $this->getCommentIri(1),
        ];

        $response = $client->request('POST', '/api/notes', [
            'json' => $note,
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(403);
    }

    public function testMemberShouldBeAbleToRateCommentSomeoneElse(): void
    {
        $client = static::createClient();
        $token  = $this->getToken($client, 'Alice', 'alice');

        $note = [
            'rate' => 5,
            'comment' => $this->getCommentIri(0),
        ];

        $response = $client->request('POST', '/api/notes', [
            'json' => $note,
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($note);
        $this->removeNotes();
    }

    protected function setUp(): void
    {
        static::bootKernel();
        $this->initializeComments();
    }

    protected function tearDown(): void
    {
        $this->removeComments();
    }
}
