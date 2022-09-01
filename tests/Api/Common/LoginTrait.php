<?php

declare(strict_types=1);

namespace App\Tests\Api\Common;

use Symfony\Contracts\HttpClient\HttpClientInterface;

trait LoginTrait
{
    protected function getToken(HttpClientInterface $client, string $username, string $password): string
    {
        $response = $client->request(
            'POST',
            '/api/login_check',
            [
                'json' => [
                    'username' => $username,
                    'password' => $password,
                ],
                'headers' => [
                    'CONTENT_TYPE' => 'application/json',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $data = \json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        return $data['token'];
    }
}
