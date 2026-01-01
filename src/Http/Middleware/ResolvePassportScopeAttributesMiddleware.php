<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Laravel\Passport\Contracts\OAuthenticatable;
use N3XT0R\PassportModernScopes\Support\Resolver\Routing\ControllerActionResolver;
use N3XT0R\PassportModernScopes\Support\Resolver\Attributes\ScopeAttributeResolver;
use N3XT0R\PassportModernScopes\Support\Resolver\Security\ScopeEvaluator;

/**
 * Middleware to resolve and enforce scope-related attributes on controller actions.
 * @package N3XT0R\PassportModernScopes\Http\Middleware
 * @author Ilya Beliaev <info@php-dev.info>
 */
final readonly class ResolvePassportScopeAttributesMiddleware
{
    public function __construct(
        private ControllerActionResolver $actionResolver,
        private ScopeAttributeResolver $attributeResolver,
        private ScopeEvaluator $scopeEvaluator,
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        if (!$route instanceof Route) {
            return $next($request);
        }

        $user = $request->user();

        if (!$user instanceof OAuthenticatable) {
            abort(401, 'Unauthenticated.');
        }

        $action = $this->actionResolver->resolve($route);
        if ($action === null) {
            return $next($request);
        }

        [$controller, $method] = $action;

        foreach ($this->attributeResolver->resolve($controller, $method) as $attribute) {
            if (!$this->scopeEvaluator->allows($user, $attribute)) {
                abort(403, 'Invalid scope.');
            }
        }

        return $next($request);
    }
}
