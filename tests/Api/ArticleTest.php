<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Api\Common\LoginTrait;

class ArticleTest extends ApiTestCase
{
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
            '@type' => 'Article',
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
}
