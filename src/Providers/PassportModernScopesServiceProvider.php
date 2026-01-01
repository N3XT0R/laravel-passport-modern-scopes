<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Providers;

use Illuminate\Support\ServiceProvider;
use N3XT0R\PassportModernScopes\Http\Middleware\ResolvePassportScopeAttributes;
use N3XT0R\PassportModernScopes\Support\Middleware\GroupInjector;
use Illuminate\Contracts\Http\Kernel;

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

        $this->app->afterResolving(
            Kernel::class,
            function () {
                $this->bootMiddleware();
            }
        );
    }

    protected function bootMiddleware(): void
    {
        $this->app->bind(GroupInjector::class);
        $this->app->make(GroupInjector::class)
            ->inject(
                ResolvePassportScopeAttributes::class
            );

        if ($this->app['config']->get('passport-modern-scopes.auto_boot.enabled', false)) {
            $router = $this->app['router'];
            if (!array_key_exists('api', $router->getMiddlewareGroups())) {
                $router->middlewareGroup('api', []);
            }
            $router->pushMiddlewareToGroup('api', ResolvePassportScopeAttributes::class);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/passport-modern-scopes.php',
            'passport-modern-scopes'
        );

        $this->app->singleton(ResolvePassportScopeAttributes::class);
    }
}