<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Api\Common\LoginTrait;
use App\Tests\Common\ArticleTrait;
use App\Tests\Common\CommentTrait;

class ArticleTest extends ApiTestCase
{
    use ArticleTrait;
    use CommentTrait;
    use LoginTrait;

    public function testOnlyAdminShouldBeAbleCreateArticle(): void
    {
        $client  = static::createClient();
        $article = [
            'published' => true,
            'title' => 'The best article in the world',
            'content' => 'The best article in the world',
        ];

        $token    = $this->getToken($client, 'Bob', 'bob');
        $response = $client->request('POST', '/api/articles', [
            'json' => $article,
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'published' => true,
            'title' => 'The best article in the world',
            'content' => 'The best article in the world',
        ]);

        $token    = $this->getToken($client, 'Alice', 'alice');
        $response = $client->request('POST', '/api/articles', [
            'json' => $article,
            'auth_bearer' => $token,
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testMemberShouldBeAbleToListArticles(): void
    {
        $client = static::createClient();
        $token  = $this->getToken($client, 'Alice', 'alice');

        $response = $client->request('GET', '/api/articles', [
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'page' => 1,
            'numberByPage' => 10,
            'numberTotalItem' => 15,
            'numberPage' => 2,
        ]);
        $json = \json_decode($response->getContent(), true);
        $this->assertCount(10, $json['data']);

        $response = $client->request('GET', '/api/articles?page=2', [
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'page' => 2,
            'numberByPage' => 10,
            'numberTotalItem' => 15,
            'numberPage' => 2,
        ]);
        $json = \json_decode($response->getContent(), true);
        $this->assertCount(5, $json['data']);
    }

    public function testMemberShouldBeAbleToReadArticle(): void
    {
        $client = static::createClient();
        $token  = $this->getToken($client, 'Alice', 'alice');

        $response = $client->request('GET', '/api/articles/' . $this->articles[0]->getId(), [
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'published' =>  false,
            'title' =>  'The best article in the world 1',
            'content' =>  'The best article in the world 1',
        ]);
    }

    public function testAdminShouldBeAbleToEditArticle(): void
    {
        $client = static::createClient();
        $token  = $this->getToken($client, 'Bob', 'bob');

        $article = [
            'content' => 'The best article in the univers',
        ];

        $response = $client->request('PUT', '/api/articles/' . $this->articles[0]->getId(), [
            'json' => $article,
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains($article);
    }

    public function testAdminShouldBeAbleToDeleteArticle(): void
    {
        $client = static::createClient();
        $this->removeComments();
        $token = $this->getToken($client, 'Bob', 'bob');

        $response = $client->request('DELETE', '/api/articles/' . $this->articles[0]->getId(), [
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(204);
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
