<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Tests\Fixtures\Http\Controllers;

use N3XT0R\PassportModernScopes\Support\Attributes\RequiresAnyScope;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresScope;

final class ScopeAttributeController
{
    #[RequiresScope(['read', 'write'])]
    public function requiresAll(): string
    {
        $this->ensureAllScopes(['read', 'write']);

        return 'ok';
    }

    #[RequiresAnyScope(['admin', 'editor'])]
    public function requiresAny(): string
    {
        $this->ensureAnyScope(['admin', 'editor']);

        return 'ok';
    }

    private function ensureAllScopes(array $scopes): void
    {
        foreach ($scopes as $scope) {
            abort_unless(request()->user()?->tokenCan($scope), 403);
        }
    }

    private function ensureAnyScope(array $scopes): void
    {
        abort_unless(
            collect($scopes)->contains(fn (string $scope): bool => request()->user()?->tokenCan($scope)),
            403
        );
    }
}
