<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractWebTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected User $user;
    protected Session $session;

    protected function getBrowser(): KernelBrowser
    {
        return $this->client;
    }

    protected function getRouter(): RouterInterface
    {
        return static::getContainer()->get('router');
    }

    protected function getClientAuthorizationChecker(): AuthorizationChecker
    {
        return $this->getBrowser()->getContainer()->get('security.authorization_checker');
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->enableProfiler();
        $this->session = static::getContainer()->get('session');
    }

    protected function logIn(string $username, string $password, string $firewallContext = 'main') : void
    {
        $this->user = static::getContainer()->get(UserRepository::class)->findOneBy(['username' => $username]);

        if (!$this->user) {
            throw new \RuntimeException('Cannot find user with username: ' . $username);
        }

        $token = new UsernamePasswordToken($this->user, $password, $firewallContext, $this->user->getRoles());
        $this->session->set('_security_'.$firewallContext, serialize($token));
        $this->session->save();

        $cookie = new Cookie($this->session->getName(), $this->session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    protected function loginSampleUser(string $firewallContext = 'main'): void
    {
        $user = static::getContainer()->get(UserRepository::class)->getTestUser();
        $this->logIn($user->getUsername(), (string)$user->getPassword(), $firewallContext);
    }

    protected function logOut() : void
    {
        $this->session->clear();
        $this->client->getCookieJar()->clear();
    }

    /**
     * @return User
     */
    protected function getCurrentUser() : ?UserInterface
    {
        return $this->user;
    }

    protected function getDoctrine(): Registry
    {
        return static::getContainer()->get('doctrine');
    }
}
