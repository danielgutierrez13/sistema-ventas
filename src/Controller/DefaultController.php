<?php

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\App\Infrastructure\Controller\WebController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends WebController
{
    #[Route(path: '/', name: 'homepage', methods: ['GET', 'POST'])]
    public function home(Request $request): Response
    {
        return $this->render('default/homepage.html.twig', [
        ]);
    }
}
