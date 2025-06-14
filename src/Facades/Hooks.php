<?php

namespace CreativeSoftTechSolutions\LaravelHooks\Facades;

use Illuminate\Support\Facades\Facade;

class Hooks extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Hooks';
    }
}
