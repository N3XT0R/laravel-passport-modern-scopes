<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Tests\Fixtures\Attributes;

use N3XT0R\PassportModernScopes\Support\Attributes\RequiresAnyScope;

#[RequiresAnyScope(['admin'])]
final class RequiresAnyScopeTestFixture
{
    #[RequiresAnyScope(['user', 'editor'])]
    public function securedMethod(): void
    {
    }
}
