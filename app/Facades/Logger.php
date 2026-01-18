<?php

namespace App\Facades;

use App\Services\LoggerService;
use Illuminate\Support\Facades\Facade;

class Logger extends Facade
{
    protected static function getFacadeAccessor()
    {
        return LoggerService::class;
    }
}
