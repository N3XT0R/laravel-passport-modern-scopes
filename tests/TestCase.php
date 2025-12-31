<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Tests;

use Laravel\Passport\PassportServiceProvider;
use N3XT0R\PassportModernScopes\Providers\PassportModernScopesServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{

    public function getPackageProviders($app): array
    {
        return [
            PassportServiceProvider::class,
            PassportModernScopesServiceProvider::class,
        ];
    }
}