<?php

declare(strict_types=1);

namespace Djvue\WpAdminBundle\Controller;

use Djvue\WpAdminBundle\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class WpJsonController
{
    public function __construct(
        private LoaderInterface $loader,
    ) {
    }

    public function __invoke(): Response
    {
        $this->loader->loadCore();
        if (!current_user_can('edit_posts')) {
            return new JsonResponse(['error' => 'forbidden'], Response::HTTP_FORBIDDEN);
        }
        $this->loader->terminate();
        return new Response();
    }
}
