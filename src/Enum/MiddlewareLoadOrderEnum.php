<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Enum;

enum MiddlewareLoadOrderEnum: string
{
    case PREPEND = 'prepend';
    case APPEND = 'append';
    case CUSTOM = 'custom';
}
