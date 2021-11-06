<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends AbstractFOSRestController
{
    public function index(): Response
    {
        return $this->handleView($this->view(['Hello World!']));
    }

    public function test(): Response
    {
        return $this->handleView($this->view(['test']));
    }
}
