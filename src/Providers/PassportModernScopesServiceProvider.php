<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Providers;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use N3XT0R\PassportModernScopes\Http\Middleware\ResolvePassportScopeAttributes;

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


        if (!($config->get('auto_boot.enabled') ?? true)) {
            return;
        }


        $this->app['router']->pushMiddlewareToGroup(
            $config->get('auto_boot.middleware_group', 'api'),
            ResolvePassportScopeAttributes::class
        );
    }
}