<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Support\Resolver\Routing;

use Illuminate\Routing\Route;

/**
 * Resolves controller class and action method from a Laravel route.
 * @package N3XT0R\PassportModernScopes\Support\Resolver\Routing
 * @author Ilya Beliaev <info@php-dev.info>
 *
 */
final class ControllerActionResolver
{
    /**
     * Resolve the controller and method from the given route.
     * @param Route $route
     * @return array{0: class-string, 1: string}|null
     */
    public function resolve(Route $route): ?array
    {
        $action = $route->getAction('controller');

        return match (true) {
            is_string($action) && str_contains($action, '@') => explode('@', $action, 2),
            is_array($action) && count($action) === 2 => $action,
            default => null,
        };
    }
}
