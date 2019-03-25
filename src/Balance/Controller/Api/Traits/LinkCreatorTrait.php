<?php

namespace App\Balance\Controller\Api\Traits;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait LinkCreatorTrait
{
    protected function generateLink(string $route, array $params, ?int $page = null): string
    {
        unset($params['offset'], $params['limit']);

        if ($page) {
            $params['page'] = $page;
        }

        return $this->generateUrl($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
