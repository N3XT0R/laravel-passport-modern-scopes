<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Tests\Fixtures\Attributes;

use N3XT0R\PassportModernScopes\Support\Attributes\RequiresScope;

#[RequiresScope(['admin'])]
final class RequiresScopeTestFixture
{
    #[RequiresScope(['user', 'editor'])]
    public function securedMethod(): void
    {
    }
}
