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
        return 'ok';
    }

    #[RequiresAnyScope(['admin', 'editor'])]
    public function requiresAny(): string
    {
        return 'ok';
    }
}
