<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Tests\Feature\Http\Controllers;

use App\Models\User;
use Laravel\Passport\Passport;
use N3XT0R\PassportModernScopes\Tests\PassportTestCase;

class ExampleScopedControllerTest extends PassportTestCase
{

    protected string $route = '/example-scoped';

    protected function setUp(): void
    {
        parent::setUp();
        $this->app['config']->set('passport-modern-scopes.auto_boot.enabled', true);
    }

    /* -----------------------------------------------------------------
     | INDEX / SHOW / STORE (example:read)
     |-----------------------------------------------------------------*/

    public function testIndexWithReadScopeIsAllowed(): void
    {
        Passport::actingAs(User::factory()->create(), ['example:read']);

        $this->getJson($this->route)
            ->assertOk();
    }

    public function testIndexWithoutScopeIsForbidden(): void
    {
        Passport::actingAs(User::factory()->create(), []);

        $this->getJson($this->route)
            ->assertForbidden();
    }

    public function testShowWithReadScopeIsAllowed(): void
    {
        Passport::actingAs(User::factory()->create(), ['example:read']);

        $this->getJson($this->route . '/1')
            ->assertOk();
    }

    public function testShowWithoutScopeIsForbidden(): void
    {
        Passport::actingAs(User::factory()->create(), []);

        $this->getJson($this->route . '/1')
            ->assertForbidden();
    }

    public function testStoreWithReadScopeIsAllowed(): void
    {
        Passport::actingAs(User::factory()->create(), ['example:read']);

        $this->postJson($this->route)
            ->assertCreated();
    }

    public function testStoreWithoutScopeIsForbidden(): void
    {
        Passport::actingAs(User::factory()->create(), []);

        $this->postJson($this->route)
            ->assertForbidden();
    }

    /* -----------------------------------------------------------------
     | UPDATE (example:read + example:update)
     |-----------------------------------------------------------------*/

    public function testUpdateWithReadAndUpdateScopeIsAllowed(): void
    {
        Passport::actingAs(
            User::factory()->create(),
            ['example:read', 'example:update']
        );

        $this->putJson($this->route . '/1')
            ->assertStatus(202);
    }

    public function testUpdateWithOnlyReadScopeIsForbidden(): void
    {
        Passport::actingAs(
            User::factory()->create(),
            ['example:read']
        );

        $this->putJson($this->route . '/1')
            ->assertForbidden();
    }

    public function testUpdateWithoutScopesIsForbidden(): void
    {
        Passport::actingAs(User::factory()->create(), []);

        $this->putJson($this->route . '/1')
            ->assertForbidden();
    }

    /* -----------------------------------------------------------------
     | DESTROY (example:read + example:delete)
     |-----------------------------------------------------------------*/

    public function testDestroyWithReadAndDeleteScopeIsAllowed(): void
    {
        Passport::actingAs(
            User::factory()->create(),
            ['example:read', 'example:delete']
        );

        $this->deleteJson($this->route . '/1')
            ->assertNoContent();
    }

    public function testDestroyWithOnlyReadScopeIsForbidden(): void
    {
        Passport::actingAs(
            User::factory()->create(),
            ['example:read']
        );

        $this->deleteJson($this->route . '/1')
            ->assertForbidden();
    }

    public function testDestroyWithoutScopesIsForbidden(): void
    {
        Passport::actingAs(User::factory()->create(), []);

        $this->deleteJson($this->route . '/1')
            ->assertForbidden();
    }
}
