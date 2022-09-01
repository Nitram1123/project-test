<?php

declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class MemberTest extends ApiTestCase
{
    public function testMemberShouldHaveBearerTokenToAccessProtectedResource(): void
    {
        $client   = static::createClient();
        $response = $client->request(
            'POST',
            '/api/login_check',
            [
                'json' => [
                    'username' => 'Alice',
                    'password' => 'alice',
                ],
                'headers' => [
                    'CONTENT_TYPE' => 'application/json',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $data = \json_decode($response->getContent(), true);
        $this->assertArrayHasKey('token', $data);

        $client->request(
            'GET',
            '/api/members/roles',
            [
                'auth_bearer' => $data['token'],
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([['ROLE_USER']]);

        $client->request('GET', '/api/members/roles');
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }
}
