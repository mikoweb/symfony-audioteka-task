<?php
/*
 * Copyright (c) Rafał Mikołajun 2021.
 */

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class IndexController extends AbstractFOSRestController
{
    #[IsGranted('ROLE_API_USER')]
    public function index(): Response
    {
        throw $this->createAccessDeniedException();
    }
}
