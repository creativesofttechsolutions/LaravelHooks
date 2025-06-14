<?php

namespace CreativeSoftTechSolutions\LaravelHooks\Services;

use Illuminate\Contracts\View\View as ViewContract;

class HooksService
{
    protected array $actions = [];

    protected array $filters = [];

    protected array $views = [];

    // Register an action hook
    public function addAction(string $hook, callable $callback, int $priority = 10): void
    {
        $this->actions[$hook][$priority][] = $callback;
    }

    // Register a filter hook
    public function addFilter(string $hook, callable|string|array $callback, int $priority = 10): void
    {
        $this->filters[$hook][$priority][] = function ($value, ...$args) use ($callback) {
            if (is_array($callback) && count($callback) === 2 && is_string($callback[0]) && is_string($callback[1])) {
                // Handle [ClassName::class, 'methodName']
                $instance = app($callback[0]);

                return $instance->{$callback[1]}($value, ...$args);
            }

            if (is_string($callback) && str_contains($callback, '@')) {
                // Handle 'ClassName@methodName'
                [$class, $method] = explode('@', $callback);
                $instance = app($class);

                return $instance->{$method}($value, ...$args);
            }

            // Fallback for closures and other callables
            return call_user_func($callback, $value, ...$args);
        };
    }

    // Register a view hook
    // public function addView(string $hook, callable $callback, int $priority = 10): void
    // {
    //     $this->views[$hook][$priority][] = $callback;
    // }
    public function addView(string $hook, callable|string|array $callback, int $priority = 10): void
    {
        $this->views[$hook][$priority][] = function (...$args) use ($callback) {
            // Handle callable array format: [ClassName::class, 'methodName']
            if (is_array($callback) && count($callback) === 2 && is_string($callback[0]) && is_string($callback[1])) {
                // Resolve the class and call the method dynamically
                $instance = app($callback[0]); // Resolve the class instance

                return app()->call([$instance, $callback[1]], ['args' => $args]);
            }

            // Handle callable as a string (e.g., 'Controller@method')
            if (is_string($callback) && str_contains($callback, '@')) {
                [$class, $method] = explode('@', $callback);
                $instance = app($class);

                return app()->call([$instance, $method], ['args' => $args]);
            }

            // Fallback: Call the provided callback directly
            return call_user_func_array($callback, $args);
        };
    }

    // Execute all action hooks
    public function doAction(string $hook, mixed ...$args): void
    {
        if (! isset($this->actions[$hook])) {
            return;
        }

        foreach ($this->getCallbacks($this->actions[$hook]) as $callback) {
            // call_user_func_array($callback, $args);
            try {
                call_user_func_array($callback, $args);
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
        }

    }

    // Apply filter hooks to data
    public function applyFilters(string $hook, mixed $value = [], mixed ...$args): mixed
    {
        if (! isset($this->filters[$hook])) {
            return $value;
        }

        foreach ($this->getCallbacks($this->filters[$hook]) as $callback) {
            $result = call_user_func_array($callback, [$value, ...$args]);
            if (is_array($value) && is_array($result)) {
                // Merge arrays instead of overwriting
                $value = array_merge($value, $result);
            } else {
                $value = $result;
            }
        }

        return $value;
    }

    // Render view-based hook content
    public function renderViewHook(string $hook, mixed ...$args): string
    {
        if (! isset($this->views[$hook])) {
            return '';
        }

        $output = '';

        foreach ($this->getCallbacks($this->views[$hook]) as $callback) {
            $result = call_user_func_array($callback, $args);

            if (is_string($result)) {
                $output .= $result;
            } elseif ($result instanceof ViewContract) {
                $output .= $result->render();
            }
        }

        return $output;
    }

    public function applyMultipleFilters(string $hook, array $initialPayload = []): array
    {
        if (! isset($this->filters[$hook])) {
            return $initialPayload;
        }

        foreach ($this->getCallbacks($this->filters[$hook]) as $callback) {
            $result = call_user_func_array($callback, [$initialPayload]);
            if (is_array($result)) {
                $initialPayload = array_merge($initialPayload, $result);
            }
        }

        return $initialPayload;
    }

    // Sort callbacks by priority
    protected function getCallbacks(array $callbacks): array
    {
        ksort($callbacks);

        return array_merge(...array_values($callbacks));
    }

    /**
     * Get all registered action hooks.
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Get all registered filter hooks.
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Get all registered view hooks.
     */
    public function getViews(): array
    {
        return $this->views;
    }

    /**
     * Check if an action hook is registered.
     *
     * @param  string  $hook  The name of the hook to check.
     */
    public function hasAction(string $hook): bool
    {
        return isset($this->actions[$hook]);
    }

    /**
     * Check if a filter hook is registered.
     *
     * @param  string  $hook  The name of the hook to check.
     */
    public function hasFilter(string $hook): bool
    {
        return isset($this->filters[$hook]);
    }

    /**
     * Check if a view hook is registered.
     *
     * @param  string  $hook  The name of the hook to check.
     */
    public function hasView(string $hook): bool
    {
        return isset($this->views[$hook]);
    }
}
