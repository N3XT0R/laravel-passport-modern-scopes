<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Support;

use Illuminate\Config\Repository;
use Illuminate\Routing\Router;
use LogicException;
use N3XT0R\PassportModernScopes\Enum\MiddlewareLoadOrderEnum;

final class GroupInjector
{
    private const string CONFIG_PREFIX = 'passport-modern-scopes.auto_boot';

    public function __construct(
        private readonly Router $router,
        private readonly Repository $config,
    ) {
    }

    /**
     * Inject middleware into the specified group based on configuration.
     * @param string $middlewareClass
     * @param string $group
     * @return void
     */
    public function inject(
        string $middlewareClass,
        string $group
    ): void {
        if (!$this->config->get(self::CONFIG_PREFIX . '.enabled', false)) {
            return;
        }

        $order = $this->resolveOrder();

        match ($order) {
            MiddlewareLoadOrderEnum::PREPEND => $this->prepend($middlewareClass, $group),
            MiddlewareLoadOrderEnum::APPEND => $this->append($middlewareClass, $group),
            MiddlewareLoadOrderEnum::CUSTOM => $this->custom($middlewareClass, $group),
        };
    }

    /**
     * Resolve the middleware load order from configuration.
     * @return MiddlewareLoadOrderEnum
     */
    private function resolveOrder(): MiddlewareLoadOrderEnum
    {
        $value = $this->config->get(
            self::CONFIG_PREFIX . '.order',
            MiddlewareLoadOrderEnum::APPEND->value
        );

        return $value instanceof MiddlewareLoadOrderEnum
            ? $value
            : MiddlewareLoadOrderEnum::from((string)$value);
    }

    /**
     * Prepend middleware to the beginning of the group.
     * @param string $middleware
     * @param string $group
     * @return void
     */
    private function prepend(string $middleware, string $group): void
    {
        $this->router->prependMiddlewareToGroup($group, $middleware);
    }

    /**
     * Append middleware to the end of the group.
     * @param string $middleware
     * @param string $group
     * @return void
     */
    private function append(string $middleware, string $group): void
    {
        $this->router->pushMiddlewareToGroup($group, $middleware);
    }

    /**
     * Inject middleware at a custom position in the group.
     * @param string $middleware
     * @param string $group
     * @return void
     */
    private function custom(string $middleware, string $group): void
    {
        $before = $this->config->get(
            self::CONFIG_PREFIX . '.custom_position.before'
        );

        if (!$before) {
            throw new LogicException(
                'CUSTOM middleware order requires passport-modern-scopes.auto_boot.custom_position.before'
            );
        }

        $groups = $this->router->getMiddlewareGroups();

        if (!isset($groups[$group])) {
            throw new LogicException("Middleware group [$group] does not exist.");
        }

        $middlewares = $groups[$group];
        $index = array_search($before, $middlewares, true);

        if ($index === false) {
            throw new LogicException(
                "Target middleware [$before] not found in group [$group]."
            );
        }

        array_splice($middlewares, $index, 0, [$middleware]);

        $this->router->middlewareGroup($group, $middlewares);
    }
}
