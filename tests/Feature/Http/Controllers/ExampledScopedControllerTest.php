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


    protected function defineRoutes($router): void
    {
        $router->middleware('api')
            ->apiResource($this->route, ExampleScopedController::class);
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