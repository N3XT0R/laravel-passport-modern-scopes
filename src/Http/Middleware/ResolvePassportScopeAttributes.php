<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresAnyScope;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresScope;
use ReflectionMethod;

final class ResolvePassportScopeAttributes
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        if (!$route instanceof Route) {
            return $next($request);
        }

        foreach ($this->resolveScopeAttributes($route) as $attribute) {
            if ($attribute instanceof RequiresScope
                && !$this->tokenHasAll($request, $attribute->scopes)
            ) {
                abort(403, 'Invalid scope.');
            }

            if ($attribute instanceof RequiresAnyScope
                && !$this->tokenHasAny($request, $attribute->scopes)
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
        $action = $route->getAction('controller');

        if (is_string($action) && str_contains($action, '@')) {
            [$controller, $method] = explode('@', $action, 2);
        } elseif (is_array($action) && count($action) === 2) {
            [$controller, $method] = $action;
        } else {
            return [];
        }

        if (!is_string($controller) || !method_exists($controller, $method)) {
            return [];
        }

        $reflection = new ReflectionMethod($controller, $method);
        $attributes = [];

        foreach ($reflection->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();

            if ($instance instanceof RequiresScope || $instance instanceof RequiresAnyScope) {
                $attributes[] = $instance;
            }
        }

        return $attributes;
    }

    /**
     * Determine if the token has all of the given scopes.
     * @param Request $request
     * @param array $scopes
     * @return bool
     */
    private function tokenHasAll(Request $request, array $scopes): bool
    {
        return array_all($scopes, fn($scope) => $this->tokenCan($request, $scope));
    }

    /**
     * Determine if the token has any of the given scopes.
     * @param Request $request
     * @param array $scopes
     * @return bool
     */
    private function tokenHasAny(Request $request, array $scopes): bool
    {
        foreach ($scopes as $scope) {
            if ($this->tokenCan($request, $scope)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the token has the given scope.
     * @param Request $request
     * @param string $scope
     * @return bool
     */
    private function tokenCan(Request $request, string $scope): bool
    {
        $user = $request->user();

        if ($user === null || !method_exists($user, 'tokenCan')) {
            return false;
        }
        return $user->tokenCan($scope);
    }
}