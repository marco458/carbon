<?php

declare(strict_types=1);

namespace Core\Service;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class RouteVersionMapper.
 *
 * Maps certain routes to their same-version-equivalent landing routes.
 * I.e.:
 *
 * - when requesting Reset Password in v2, your reset password landing page should be v2 landing page
 *
 * @author Ante Crnogorac<ante@q.agency>
 */
final class RouteVersionMapper
{
    /**
     * @var array|string[]
     */
    private array $routeMappings = [
        'api_v1_magic_link_request' => 'api_v1_magic_link_login',
    ];

    public function resolve(Request $request): ?string
    {
        $currentRoute = $request->get('_route');

        if ('' === $currentRoute || null === $currentRoute) {
            return null;
        }

        return $this->routeMappings[$currentRoute];
    }
}
