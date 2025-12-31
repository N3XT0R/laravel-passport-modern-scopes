<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Support\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final readonly class RequiresAnyScope
{
    /**
     * @param string[] $scopes
     */
    public function __construct(
        public array $scopes
    ) {
    }
}
