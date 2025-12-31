<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Tests\Unit\Support\Attributes;

use N3XT0R\PassportModernScopes\Support\Attributes\RequiresScope;
use N3XT0R\PassportModernScopes\Tests\Fixtures\Attributes\RequiresScopeTestFixture;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

final class RequiresScopeTest extends TestCase
{
    public function testItStoresScopesCorrectly(): void
    {
        $scopes = ['read', 'write'];

        $attribute = new RequiresScope($scopes);

        $this->assertSame($scopes, $attribute->scopes);
    }

    public function testItCanBeUsedOnAClass(): void
    {
        $reflection = new ReflectionClass(RequiresScopeTestFixture::class);
        $attributes = $reflection->getAttributes(RequiresScope::class);

        $this->assertCount(1, $attributes);

        /** @var RequiresScope $instance */
        $instance = $attributes[0]->newInstance();

        $this->assertSame(['admin'], $instance->scopes);
    }

    public function testItCanBeUsedOnAMethod(): void
    {
        $reflection = new ReflectionMethod(
            RequiresScopeTestFixture::class,
            'securedMethod'
        );

        $attributes = $reflection->getAttributes(RequiresScope::class);

        $this->assertCount(1, $attributes);

        /** @var RequiresScope $instance */
        $instance = $attributes[0]->newInstance();

        $this->assertSame(['user', 'editor'], $instance->scopes);
    }

    public function testScopesAreReadonly(): void
    {
        $this->expectException(\Error::class);

        $attribute = new RequiresScope(['read']);
        $attribute->scopes = ['write'];
    }
}
