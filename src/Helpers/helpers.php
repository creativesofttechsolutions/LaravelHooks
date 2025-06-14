<?php

use CreativeSoftTechSolutions\LaravelHooks\Facades\Hooks;
use CreativeSoftTechSolutions\LaravelHooks\Services\HooksService;

if (! function_exists('hooks')) {
    function hooks(): HooksService
    {
        return app(HooksService::class);
    }
}

if (! function_exists('addFilter')) {
    function addFilter(string $hook, callable|string|array $callback, int $priority = 10): void
    {
        hooks()->addFilter($hook, $callback, $priority);
    }
}

if (! function_exists('applyFilters')) {
    function applyFilters(string $hook, mixed $value, mixed ...$args): mixed
    {
        return hooks()->applyFilters($hook, $value, $args);
    }
}
if (! function_exists('apply_filters')) {
    function apply_filters(string $hook, mixed $value, mixed ...$args): mixed
    {
        return hooks()->applyFilters($hook, $value, $args);
    }
}

if (! function_exists('addAction')) {
    function addAction(string $hook, callable $callback, int $priority = 10): void
    {
        hooks()->addAction($hook, $callback, $priority);
    }
}

if (! function_exists('doAction')) {
    function doAction(string $hook, mixed ...$args): void
    {
        hooks()->doAction($hook, ...$args);
    }
}

if (! function_exists('addView')) {
    function addView(string $hook, callable|string|array $callback, int $priority = 10): void
    {
        hooks()->addView($hook, $callback, $priority);
    }
}

if (! function_exists('renderViewHook')) {
    function renderViewHook(string $hook, mixed ...$args): string
    {
        return hooks()->renderViewHook($hook, ...$args);
    }
}

if (! function_exists('applyMultipleFilters')) {
    function applyMultipleFilters(string $hook, array $initialPayload = []): array
    {
        return hooks()->applyMultipleFilters($hook, $initialPayload);
    }
}
