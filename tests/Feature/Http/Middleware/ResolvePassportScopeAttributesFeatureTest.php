<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Tests\Feature\Http\Middleware;

use App\Models\User;
use Laravel\Passport\Passport;
use N3XT0R\PassportModernScopes\Tests\Fixtures\Http\Controllers\ScopeAttributeController;
use N3XT0R\PassportModernScopes\Tests\PassportTestCase;

final class ResolvePassportScopeAttributesFeatureTest extends PassportTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Routen laufen durch die api-Middleware-Group
        $this->app['router']->middleware('api')->group(function () {
            $this->app['router']->get(
                '/requires-all',
                ScopeAttributeController::class . '@requiresAll'
            );

            $this->app['router']->get(
                '/requires-any',
                ScopeAttributeController::class . '@requiresAny'
            );
        });
    }

    public function testRequiresScopeDeniesAccessWithoutAllScopes(): void
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
