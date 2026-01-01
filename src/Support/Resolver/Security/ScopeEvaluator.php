<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Support\Resolver\Security;

use Laravel\Passport\Contracts\OAuthenticatable;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresAnyScope;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresScope;

/**
 * Evaluates whether a user has the required scopes based on scope-related attributes.
 * @package N3XT0R\PassportModernScopes\Support\Resolver\Security
 * @author Ilya Beliaev <info@php-dev.info>
 */
final class ScopeEvaluator
{
    public function allows(OAuthenticatable $user, object $attribute): bool
    {
        return match (true) {
            $attribute instanceof RequiresScope =>
            $this->hasAll($user, $attribute->scopes),

            $attribute instanceof RequiresAnyScope =>
            $this->hasAny($user, $attribute->scopes),

            default => true,
        };
    }

    private function hasAll(OAuthenticatable $user, array $scopes): bool
    {
        return array_all($scopes, fn($scope) => $user->tokenCan($scope));
    }

    private function hasAny(OAuthenticatable $user, array $scopes): bool
    {
        return array_any($scopes, fn($scope) => $user->tokenCan($scope));
    }
}
