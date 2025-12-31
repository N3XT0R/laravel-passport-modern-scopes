<?php

declare(strict_types=1);

use N3XT0R\PassportModernScopes\Enum\MiddlewareLoadOrderEnum;

return [
    'auto_boot' => [
        'enabled' => true,
        'middleware_group' => 'api',
        'order' => MiddlewareLoadOrderEnum::APPEND->value, // prepend | append | custom
        'custom_position' => [
            'before' => \Laravel\Passport\Http\Middleware\CheckToken::class,
        ],
    ],
];