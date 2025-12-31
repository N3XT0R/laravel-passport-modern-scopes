<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Providers;

use Illuminate\Support\ServiceProvider;
use N3XT0R\PassportModernScopes\Http\Middleware\ResolvePassportScopeAttributes;
use N3XT0R\PassportModernScopes\Support\Middleware\GroupInjector;

class PassportModernScopesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../../config/migration-generator.php' => config_path('passport-modern-scopes.php'),
            ],
            'passport-modern-scopes'
        );

        $this->bootMiddleware();
    }

    protected function bootMiddleware(): void
    {
        $this->app->bind(GroupInjector::class, GroupInjector::class);
        $this->app->make(GroupInjector::class)
            ->inject(
                ResolvePassportScopeAttributes::class
            );

        if ($this->app['config']->get('passport-modern-scopes.auto_boot.enabled', false)) {
            $this->app['router']->pushMiddlewareToGroup('api', ResolvePassportScopeAttributes::class);
            $this->app->make('Illuminate\Contracts\Http\Kernel')
                ->pushMiddleware(ResolvePassportScopeAttributes::class);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/passport-modern-scopes.php',
            'passport-modern-scopes'
        );
    }
}