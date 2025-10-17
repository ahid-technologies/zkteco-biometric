<?php

namespace AhidTechnologies\ZKTecoBiometric\Facades;

use Illuminate\Support\Facades\Facade;

class ZKTeco extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zkteco-biometric';
    }
}
