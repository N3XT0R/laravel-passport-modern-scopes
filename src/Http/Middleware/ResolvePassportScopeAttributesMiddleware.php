<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Laravel\Passport\Contracts\OAuthenticatable;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresAnyScope;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresScope;
use ReflectionMethod;
use ReflectionClass;

/**
 * Middleware to resolve Passport scope attributes on route controllers.
 * @package N3XT0R\PassportModernScopes\Http\Middleware
 * @author Ilya Beliaev <info@php-dev.info>
 */
final class ResolvePassportScopeAttributesMiddleware
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        if (!$route instanceof Route) {
            return $next($request);
        }


        $authenticatable = $request->user();

        if (!$authenticatable instanceof OAuthenticatable) {
            abort(401, 'Unauthenticated.');
        }

        foreach ($this->resolveScopeAttributes($route) as $attribute) {
            if ($attribute instanceof RequiresScope
                && !$this->tokenHasAll($authenticatable, $attribute->scopes)
            ) {
                abort(403, 'Invalid scope.');
            }

            if ($attribute instanceof RequiresAnyScope
                && !$this->tokenHasAny($authenticatable, $attribute->scopes)
            ) {
                abort(403, 'Invalid scope.');
            }
        }

        return $next($request);
    }

    /**
     * Determine which scope attributes are defined on the route controller.
     *
     * @param Route $route
     * @return array<int, RequiresScope|RequiresAnyScope>
     */
    private function resolveScopeAttributes(Route $route): array
    {
        $action = $this->resolveControllerAndActionFromRoute($route);
        if ($action === null) {
            return [];
        }

        [$controller, $method] = $action;

        if (!is_string($controller) || !method_exists($controller, $method)) {
            return [];
        }

        $attributes = [];

        $attributes = array_merge(
            $attributes,
            $this->getAttributesFromReflector(
                new ReflectionClass($controller)
            )
        );

        $attributes = array_merge(
            $attributes,
            $this->getAttributesFromReflector(
                new ReflectionMethod($controller, $method)
            )
        );

        return $attributes;
    }

    private function getAttributesFromReflector(ReflectionClass|ReflectionMethod $reflector): array
    {
        $attributes = [];
        foreach ($reflector->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();
            if ($instance instanceof RequiresScope || $instance instanceof RequiresAnyScope) {
                $attributes[] = $instance;
            }
        }
        return $attributes;
    }

    /**
     * Resolve the controller and action method from the route action.
     * @param Route $route
     * @return array|null
     */
    private function resolveControllerAndActionFromRoute(Route $route): ?array
    {
        $action = $route->getAction('controller');

        return match (true) {
            is_string($action) && str_contains($action, '@') => explode('@', $action, 2),
            is_array($action) && count($action) === 2 => $action,
            default => null,
        };
    }

    /**
     * Determine if the token has all the given scopes.
     * @param OAuthenticatable $authenticatable
     * @param array $scopes
     * @return bool
     */
    private function tokenHasAll(OAuthenticatable $authenticatable, array $scopes): bool
    {
        return array_all($scopes, fn($scope) => $authenticatable->tokenCan($scope));
    }

    /**
     * Determine if the token has any of the given scopes.
     * @param OAuthenticatable $authenticatable
     * @param array $scopes
     * @return bool
     */
    private function tokenHasAny(OAuthenticatable $authenticatable, array $scopes): bool
    {
        return array_any($scopes, fn($scope) => $authenticatable->tokenCan($scope));
    }
}