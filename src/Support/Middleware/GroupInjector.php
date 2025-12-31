<?php

declare(strict_types=1);

namespace N3XT0R\PassportModernScopes\Support;

use Illuminate\Config\Repository;
use Illuminate\Routing\Router;
use LogicException;
use N3XT0R\PassportModernScopes\Enum\MiddlewareLoadOrderEnum;

final class GroupInjector
{
    private const string CONFIG_ROOT = 'passport-modern-scopes.auto_boot';

    public function __construct(
        private readonly Router $router,
        private readonly Repository $config,
    ) {
    }

    public function inject(string $middleware): void
    {
        if (!$this->config->get(self::CONFIG_ROOT . '.enabled', false)) {
            return;
        }

        $groups = $this->config->get(self::CONFIG_ROOT . '.groups');

        if (!is_array($groups) || $groups === []) {
            throw new LogicException(
                'passport-modern-scopes.auto_boot.groups must be a non-empty array.'
            );
        }

        foreach ($groups as $group => $groupConfig) {
            $this->injectIntoGroup($middleware, $group, $groupConfig);
        }
    }

    private function injectIntoGroup(
        string $middleware,
        string $group,
        array $config
    ): void {
        $order = MiddlewareLoadOrderEnum::from(
            $config['order'] ?? throw new LogicException(
            "Missing 'order' for middleware group [$group]."
        )
        );

        match ($order) {
            MiddlewareLoadOrderEnum::PREPEND => $this->prepend($middleware, $group),
            MiddlewareLoadOrderEnum::APPEND => $this->append($middleware, $group),
            MiddlewareLoadOrderEnum::CUSTOM => $this->custom(
                $middleware,
                $group,
                $config['custom_position'] ?? []
            ),
        };
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
     * @param array $position
     * @return void
     */
    private function custom(
        string $middleware,
        string $group,
        array $position
    ): void {
        $before = $position['before'] ?? null;
        $after = $position['after'] ?? null;

        if (!$before && !$after) {
            throw new LogicException(
                "CUSTOM order for group [$group] requires 'before' or 'after'."
            );
        }

        $groups = $this->router->getMiddlewareGroups();

        if (!isset($groups[$group])) {
            throw new LogicException("Middleware group [$group] does not exist.");
        }

        $middlewares = $groups[$group];
        $target = $before ?? $after;

        $index = array_search($target, $middlewares, true);

        if ($index === false) {
            throw new LogicException(
                "Target middleware [$target] not found in group [$group]."
            );
        }

        $offset = $before ? 0 : 1;

        array_splice($middlewares, $index + $offset, 0, [$middleware]);

        $this->router->middlewareGroup($group, $middlewares);
    }
}
