<?php

declare(strict_types=1);

use Illuminate\Routing\Middleware\SubstituteBindings;
use N3XT0R\PassportModernScopes\Enum\MiddlewareLoadOrderEnum;

return [
    'auto_boot' => [
        'enabled' => true,
        'groups' => [
            'api' => [
                'order' => MiddlewareLoadOrderEnum::CUSTOM->value, // prepend | append | custom
                /**
                 * When using 'custom' order, specify the middleware class and its position
                 * relative to which the PassportModernScopes middleware should be placed.
                 * Example:
                 * 'custom_position' => [
                 *     'before' => \App\Http\Middleware\SomeMiddleware::class,
                 *     // or
                 *     'after' => \App\Http\Middleware\AnotherMiddleware::class,
                 * ],
                 */
                'custom_position' => [
                    //'before' => \Laravel\Passport\Http\Middleware\CheckToken::class,
                    'after' => SubstituteBindings::class,
                ],
            ],
        ],
    ],
];