<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Illuminate\Contracts\Config\Repository;
use Workbench\App\Models\User;
use Laravel\Passport\Passport;

class TestCase extends DatabaseTestCase
{
    const string KEYS = __DIR__ . '/../keys';
    const string PUBLIC_KEY = self::KEYS . '/oauth-public.key';
    const string PRIVATE_KEY = self::KEYS . '/oauth-private.key';

    protected function setUp(): void
    {
        $this->afterRefreshingDatabase();
        $this->afterApplicationCreated(function () {
            Passport::loadKeysFrom(self::KEYS);

            @unlink(self::PUBLIC_KEY);
            @unlink(self::PRIVATE_KEY);

            $this->artisan('passport:keys');
        });

        $this->beforeApplicationDestroyed(function () {
            @unlink(self::PUBLIC_KEY);
            @unlink(self::PRIVATE_KEY);
        });

        parent::setUp();
    }
}