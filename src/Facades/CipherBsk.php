<?php

namespace CipherBsk\Facades;

/**
 * Laravel Facade for CipherBsk
 * 
 * Note: This file will only be loaded when the package is installed
 * in a Laravel application. The Illuminate\Support\Facades\Facade
 * class will be available in that context.
 * 
 * @package CipherBsk\Facades
 */

use Illuminate\Support\Facades\Facade;

class CipherBsk extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \CipherBsk\CipherBsk::class;
    }
}
