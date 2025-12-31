<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Providers;

use Illuminate\Config\Repository;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use N3XT0R\PassportModernScopes\Enum\MiddlewareLoadOrderEnum;
use N3XT0R\PassportModernScopes\Http\Middleware\ResolvePassportScopeAttributes;
use N3XT0R\PassportModernScopes\Support\GroupInjector;

class PassportModernScopesServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes(
            [
                __DIR__ . '../../config/migration-generator.php' => config_path('passport-modern-scopes.php'),
            ],
            'passport-modern-scopes'
        );

        $this->bootMiddleware();
    }

    protected function bootMiddleware(): void
    {
        /**
         * @var Repository $config
         */
        $config = $this->app['config'];

        if (false === $config->get('passport-modern-scopes.auto_boot.enabled', false)) {
            return;
        }

        /**
         * @var Router $router
         */
        $router = $this->app['router'];
        $group = $config->get('passport-modern-scopes.auto_boot.middleware_group', 'api');

        $this->app->make(GroupInjector::class)
            ->inject(
                ResolvePassportScopeAttributes::class,
                $group
            );
    }
}