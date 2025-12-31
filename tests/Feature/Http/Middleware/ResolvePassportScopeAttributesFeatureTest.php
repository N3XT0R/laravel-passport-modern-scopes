<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Tests\Feature\Http\Middleware;


use Laravel\Passport\Passport;
use N3XT0R\PassportModernScopes\Tests\Fixtures\Http\Controllers\ScopeAttributeController;
use N3XT0R\PassportModernScopes\Tests\PassportTestCase;
use App\Models\User;
use Orchestra\Testbench\Concerns\WithLaravelMigrations;

final class ResolvePassportScopeAttributesFeatureTest extends PassportTestCase
{
    use WithLaravelMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']->middleware('api')->group(function () {
            $this->app['router']->get('/requires-all', [
                'uses' => [ScopeAttributeController::class, 'requiresAll'],
            ]);

            $this->app['router']->get('/requires-any', [
                'uses' => [ScopeAttributeController::class, 'requiresAny'],
            ]);
        });
    }

    public function testRequiresScopeDeniesAccessWithoutScopes(): void
    {
        Passport::actingAs(
            User::factory()->create(),
            ['read'] // write fehlt
        );

        $this->getJson('/requires-all')
            ->assertForbidden();
    }

    public function testRequiresScopeAllowsAccessWithAllScopes(): void
    {
        Passport::actingAs(
            User::factory()->create(),
            ['read', 'write']
        );

        $this->getJson('/requires-all')
            ->assertOk();
    }

    public function testRequiresAnyScopeDeniesAccessWithoutMatchingScope(): void
    {
        Passport::actingAs(
            User::factory()->create(),
            ['viewer']
        );

        $this->getJson('/requires-any')
            ->assertForbidden();
    }

    public function testRequiresAnyScopeAllowsAccessWithOneMatchingScope(): void
    {
        Passport::actingAs(
            User::factory()->create(),
            ['editor']
        );

        $this->getJson('/requires-any')
            ->assertOk();
    }
}