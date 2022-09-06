<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Api\Common\LoginTrait;
use App\Tests\Common\ArticleTrait;
use App\Tests\Common\CommentTrait;

class CommentTest extends ApiTestCase
{
    use ArticleTrait;
    use CommentTrait;
    use LoginTrait;

    public function testMemberShouldBeAbleToListCommentLinkedToArticle(): void
    {
        $client = static::createClient();
        $token  = $this->getToken($client, 'Alice', 'alice');

        $response = $client->request('GET', '/api/comments?article=' . $this->articles[0]->getId(), [
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['numberTotalItem' => 10]);
        $this->removeComments();
    }

    public function testMemberShouldBeAbleToListCommentLinkedToComment(): void
    {
        $client = static::createClient();
        $this->initializeComments();
        $token = $this->getToken($client, 'Alice', 'alice');

        $response = $client->request('GET', '/api/comments?parent=' . $this->comments[1]->getId(), [
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['numberTotalItem' => 5]);
        $this->removeComments();
    }

    public function testMemberShouldBeAbleToPostCommentLinkedToArticle(): void
    {
        $client = static::createClient();
        $token  = $this->getToken($client, 'Alice', 'alice');

        $comment = [
            'content' => 'The best comment in the world',
            'articleId' => $this->articles[0]->getId(),
        ];

        $response = $client->request('POST', '/api/comments', [
            'json' => $comment,
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'content' => 'The best comment in the world',
        ]);
        $this->removeComments();
    }

    public function testMemberShouldBeAbleToPostCommentLinkedToOtherComment(): void
    {
        $client = static::createClient();
        $this->initializeComments();
        $token = $this->getToken($client, 'Alice', 'alice');

        $comment = [
            'content' => 'The best comment in the world',
            'parentId' => $this->comments[0]->getId(),
        ];

        $response = $client->request('POST', '/api/comments', [
            'json' => $comment,
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'content' => 'The best comment in the world',
        ]);
        $this->removeComments();
    }

    public function testMemberShouldNotBeAbleToPostEmptyComment(): void
    {
        $client = static::createClient();
        $token  = $this->getToken($client, 'Alice', 'alice');

        $comment = [
            'content' => '',
        ];

        $response = $client->request('POST', '/api/comments', [
            'json' => $comment,
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'detail' => "content: This value should not be blank.\n"
                . 'parentId: A comment should be linked to an article or an other comment.',
        ]);

        $this->removeComments();
    }

    public function testMemberShouldBeAbleToEditOwnComment(): void
    {
        $client = static::createClient();
        $this->initializeComments();
        $token = $this->getToken($client, 'Alice', 'alice');

        $comment = [
            'content' => 'The best comment in the univers',
        ];

        $response = $client->request('PUT', '/api/comments/' . $this->comments[1]->getId(), [
            'json' => $comment,
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains($comment);
        $this->removeComments();
    }

    public function testAdminShouldBeAbleToApprouveComment(): void
    {
        $client = static::createClient();
        $this->initializeComments();
        $token = $this->getToken($client, 'Bob', 'bob');

        $comment = [
            'enabled' => true,
        ];

        $response = $client->request('PUT', '/api/comments/' . $this->comments[1]->getId(), [
            'json' => $comment,
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains($comment);
        $this->removeComments();
    }

    public function testMemberShouldNotBeAbleToEditCommentSomeoneElse(): void
    {
        $client = static::createClient();
        $this->initializeComments();
        $token = $this->getToken($client, 'Alice', 'alice');

        $comment = [
            'content' => 'The worst comment in the univers',
        ];

        $response = $client->request('PUT', '/api/comments/' . $this->comments[0]->getId(), [
            'json' => $comment,
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(403);
        $this->removeComments();
    }

    public function testMemberShouldBeAbleToDeleteOwnComment(): void
    {
        $client = static::createClient();
        $this->initializeComments();
        $token = $this->getToken($client, 'Alice', 'alice');

        $response = $client->request('DELETE', '/api/comments/' . $this->comments[1]->getId(), [
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(204);
        $this->removeComments();
    }

    public function testMemberShouldNotBeAbleToDeleteCommentSomeoneElse(): void
    {
        $client = static::createClient();
        $this->initializeComments();
        $token = $this->getToken($client, 'Alice', 'alice');

        $response = $client->request('DELETE', '/api/comments/' . $this->comments[0]->getId(), [
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(403);
        $this->removeComments();
    }

    protected function setUp(): void
    {
        static::bootKernel();
        $this->initializeArticles();
    }

    protected function tearDown(): void
    {
        $this->removeArticles();
    }
}
