<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */
namespace App\Security;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class ApiKeyAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new JsonResponse('Auth header required', 401);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/');
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request): array
    {
        return [
            'api_token' => $request->headers->get('Authorization'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        if (!$userProvider instanceof ApiKeyProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of ApiKeyProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        try {
            $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText(''));
            $token = $config->parser()->parse($credentials['api_token']);
        } catch (\Throwable $exception) {
            return null;
        }

        return $userProvider->loadUserByApiKey($token->claims()->get('api_key', ''));
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return in_array('ROLE_API_USER', $user->getRoles());
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
