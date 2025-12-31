<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Tests\Integration\Support\Middleware;

use LogicException;
use N3XT0R\PassportModernScopes\Enum\MiddlewareLoadOrderEnum;
use N3XT0R\PassportModernScopes\Support\Middleware\GroupInjector;
use N3XT0R\PassportModernScopes\Tests\TestCase;

final class GroupInjectorTest extends TestCase
{
    private const string TEST_MIDDLEWARE = 'test.middleware';

    protected function setUp(): void
    {
        parent::setUp();
        $this->app['router']->middlewareGroup('web', [
            'first',
            'second',
        ]);
    }

    public function testDoesNothingWhenAutoBootIsDisabled(): void
    {
        config()->set('passport-modern-scopes.auto_boot.enabled', false);

        $injector = $this->makeInjector();
        $injector->inject(self::TEST_MIDDLEWARE);

        $this->assertSame(
            ['first', 'second'],
            $this->app['router']->getMiddlewareGroups()['web']
        );
    }

    public function testAppendsMiddlewareToGroup(): void
    {
        config()->set('passport-modern-scopes.auto_boot', [
            'enabled' => true,
            'groups' => [
                'web' => [
                    'order' => MiddlewareLoadOrderEnum::APPEND->value,
                ],
            ],
        ]);

        $this->makeInjector()->inject(self::TEST_MIDDLEWARE);

        $this->assertSame(
            ['first', 'second', self::TEST_MIDDLEWARE],
            $this->app['router']->getMiddlewareGroups()['web']
        );
    }

    public function testPrependsMiddlewareToGroup(): void
    {
        config()->set('passport-modern-scopes.auto_boot', [
            'enabled' => true,
            'groups' => [
                'web' => [
                    'order' => MiddlewareLoadOrderEnum::PREPEND->value,
                ],
            ],
        ]);

        $this->makeInjector()->inject(self::TEST_MIDDLEWARE);

        $this->assertSame(
            [self::TEST_MIDDLEWARE, 'first', 'second'],
            $this->app['router']->getMiddlewareGroups()['web']
        );
    }

    public function testInsertsMiddlewareBeforeTarget(): void
    {
        config()->set('passport-modern-scopes.auto_boot', [
            'enabled' => true,
            'groups' => [
                'web' => [
                    'order' => MiddlewareLoadOrderEnum::CUSTOM->value,
                    'custom_position' => [
                        'before' => 'second',
                    ],
                ],
            ],
        ]);

        $this->makeInjector()->inject(self::TEST_MIDDLEWARE);

        $this->assertSame(
            ['first', self::TEST_MIDDLEWARE, 'second'],
            $this->app['router']->getMiddlewareGroups()['web']
        );
    }

    public function testThrowsExceptionWhenGroupsConfigIsInvalid(): void
    {
        $this->expectException(LogicException::class);

        config()->set('passport-modern-scopes.auto_boot', [
            'enabled' => true,
            'groups' => [],
        ]);

        $this->makeInjector()->inject(self::TEST_MIDDLEWARE);
    }

    public function testThrowsExceptionWhenCustomHasNoBeforeOrAfter(): void
    {
        $this->expectException(LogicException::class);

        config()->set('passport-modern-scopes.auto_boot', [
            'enabled' => true,
            'groups' => [
                'web' => [
                    'order' => MiddlewareLoadOrderEnum::CUSTOM->value,
                ],
            ],
        ]);

        $this->makeInjector()->inject(self::TEST_MIDDLEWARE);
    }

    private function makeInjector(): GroupInjector
    {
        return new GroupInjector(
            $this->app['router'],
            $this->app['config']
        );
    }
}
