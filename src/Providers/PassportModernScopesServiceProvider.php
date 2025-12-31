<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Providers;

use Illuminate\Support\ServiceProvider;
use N3XT0R\PassportModernScopes\Http\Middleware\ResolvePassportScopeAttributes;

class PassportModernScopesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['router']->pushMiddlewareToGroup(
            'api',
            ResolvePassportScopeAttributes::class
        );
    }
}