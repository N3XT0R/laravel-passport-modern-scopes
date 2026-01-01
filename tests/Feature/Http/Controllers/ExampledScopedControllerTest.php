<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Tests\Feature\Http\Controllers;

use App\Http\Controllers\ExampleScopedController;
use App\Models\User;
use Laravel\Passport\Passport;
use N3XT0R\PassportModernScopes\Tests\PassportTestCase;

class ExampledScopedControllerTest extends PassportTestCase
{

    protected string $route = '/example-scoped';

    protected function setUp(): void
    {
        parent::setUp();
        $this->app['config']->set('passport-modern-scopes.auto_boot.enabled', true);
    }


    public function testIndexRequiresReadScopeReturnsOk(): void
    {
        $user = User::factory()
            ->create();


        Passport::actingAs($user, ['example:read']);

        $this->getJson($this->route)
            ->assertOk();
    }

    public function testIndexRequiresReadScopeReturnsForbiddenWithoutScope(): void
    {
        $user = User::factory()
            ->create();

        Passport::actingAs($user);

        $this->getJson($this->route)
            ->assertForbidden();
    }
}