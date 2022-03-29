<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends BaseController
{
    #[Route(path: '/', name: 'homepage', methods: ['GET', 'POST'])]
    public function home(Request $request): Response
    {
        return $this->cacheRender('default/homepage.html.twig', [

        ]);
    }
}
