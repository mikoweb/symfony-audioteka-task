<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Tests;

use App\Repository\UserRepository;
use App\User\Query\ApiAccessTokenQuery;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

abstract class AbstractApiTestCase extends AbstractWebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginSampleUser();
    }

    protected function apiRequest(string $method, string $url, array $data = []): array
    {
        $user = static::getContainer()->get(UserRepository::class)->getTestUser();
        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($user->getApiKey()));

        $token = $config->builder()
            ->withClaim('api_key', $user->getApiKey())
            ->getToken($config->signer(), $config->signingKey())
        ;

        $this->getBrowser()->request($method, $url, $data, [],
            [
                'HTTP_Content-Type' => 'application/json',
                'HTTP_Authorization' => $token->toString(),
            ],
            json_encode($data)
        );

        return json_decode($this->getBrowser()->getResponse()->getContent(), true);
    }
}
