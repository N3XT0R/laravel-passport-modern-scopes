<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresAnyScope;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresScope;
use N3XT0R\PassportModernScopes\Support\Passport\PassportScopes;
use Closure;
use ReflectionMethod;

final class ResolvePassportScopeAttributes
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        if (!$route instanceof Route) {
            return $next($request);
        }

        $this->applyAttributes($route);

        return $next($request);
    }

    private function applyAttributes(Route $route): void
    {
        [$controller, $method] = $route->getAction('controller') ?? [null, null];

        if (!is_string($controller) || !method_exists($controller, $method)) {
            return;
        }

        $reflection = new ReflectionMethod($controller, $method);

        foreach ($reflection->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();

            match (true) {
                $instance instanceof RequiresScope =>
                $route->middleware(
                    PassportScopes::requires(...$instance->scopes)
                ),

                $instance instanceof RequiresAnyScope =>
                $route->middleware(
                    PassportScopes::requiresAny(...$instance->scopes)
                ),

                default => null,
            };
        }
    }
}