<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Tests\Unit\Support\Attributes;

use N3XT0R\PassportModernScopes\Support\Attributes\RequiresAnyScope;
use N3XT0R\PassportModernScopes\Tests\Fixtures\Attributes\RequiresAnyScopeTestFixture;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

final class RequiresAnyScopeTest extends TestCase
{
    public function testItStoresScopesCorrectly(): void
    {
        $scopes = ['read', 'write'];

        $attribute = new RequiresAnyScope($scopes);

        $this->assertSame($scopes, $attribute->scopes);
    }

    public function testItCanBeUsedOnAClass(): void
    {
        $reflection = new ReflectionClass(RequiresAnyScopeTestFixture::class);
        $attributes = $reflection->getAttributes(RequiresAnyScope::class);

        $this->assertCount(1, $attributes);

        /** @var RequiresAnyScope $instance */
        $instance = $attributes[0]->newInstance();

        $this->assertSame(['admin'], $instance->scopes);
    }

    public function testItCanBeUsedOnAMethod(): void
    {
        $reflection = new ReflectionMethod(
            RequiresAnyScopeTestFixture::class,
            'securedMethod'
        );

        $attributes = $reflection->getAttributes(RequiresAnyScope::class);

        $this->assertCount(1, $attributes);

        /** @var RequiresAnyScope $instance */
        $instance = $attributes[0]->newInstance();

        $this->assertSame(['user', 'editor'], $instance->scopes);
    }

    public function testScopesAreReadonly(): void
    {
        $this->expectException(\Error::class);

        $attribute = new RequiresAnyScope(['read']);
        $attribute->scopes = ['write'];
    }
}
