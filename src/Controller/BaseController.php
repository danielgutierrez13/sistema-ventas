<?php

declare(strict_types=1);

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Controller;

use CarlosChininin\Util\Error\Error;
use Doctrine\ORM\EntityManagerInterface;
use Pidia\Apps\Demo\Security\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    public const CACHE_TIME = 300; //seconds

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected Security $security
    ) {
    }

    protected function denyAccess(string $attribute, string $subject, object $object = null, string $message = 'Acceso denegado...'): void
    {
        $this->security->denyAccessUnlessGranted($attribute, $subject, $object, $message);
    }

    protected function hasAccess(string $attribute, string $subject, object $object = null): bool
    {
        return $this->security->hasAccess($attribute, $subject, $object);
    }

    protected function isSuperAdmin(): bool
    {
        return $this->security->isSuperAdmin();
    }

    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $parameters = array_merge($parameters, ['access' => $this->security]);

        return parent::render($view, $parameters, $response);
    }

    protected function cacheRender(string $view, array $parameters = [], int $cacheTime = self::CACHE_TIME): Response
    {
        $parameters = array_merge($parameters, ['access' => $this->security]);

        $htmlView = $this->renderView($view, $parameters);
        $response = new Response($htmlView);
        // https://symfony.com/doc/current/http_cache.html
        $response->setPublic();
        $response->setMaxAge($cacheTime);
        $response->setSharedMaxAge($cacheTime);
        // (optional) set a custom Cache-Control directive
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }

    /**
     * @param Error[] $errors
     */
    protected function addErrors(array $errors): void
    {
        foreach ($errors as $error) {
            $this->addFlash('danger', $error->message());
        }
    }
}
