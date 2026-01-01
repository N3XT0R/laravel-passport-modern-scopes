<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Support\Resolver\Attributes;

use ReflectionClass;
use ReflectionMethod;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresAnyScope;
use N3XT0R\PassportModernScopes\Support\Attributes\RequiresScope;

/**
 * Resolves scope-related attributes from controller classes and methods.
 * @package N3XT0R\PassportModernScopes\Support\Resolver\Attributes
 * @author Ilya Beliaev <info@php-dev.info>
 */
final class ScopeAttributeResolver
{
    /**
     * Resolve scope-related attributes from the given controller and method.
     * @param class-string $controller
     * @param string $method
     * @return array<RequiresScope|RequiresAnyScope>
     * @throws \ReflectionException
     */
    public function resolve(string $controller, string $method): array
    {
        return array_merge(
            $this->fromReflector(new ReflectionClass($controller)),
            $this->fromReflector(new ReflectionMethod($controller, $method))
        );
    }

    private function fromReflector(ReflectionClass|ReflectionMethod $reflector): array
    {
        $attributes = [];

        foreach ($reflector->getAttributes() as $attribute) {
            $instance = $attribute->newInstance();

            if ($instance instanceof RequiresScope || $instance instanceof RequiresAnyScope) {
                $attributes[] = $instance;
            }
        }

        return $attributes;
    }
}
