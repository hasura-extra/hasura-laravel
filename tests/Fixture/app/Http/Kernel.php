<?php

namespace Hasura\Laravel\Tests\Fixture\App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

final class Kernel extends HttpKernel
{
    protected $middlewareGroups = [
        'api' => []
    ];

    protected $routeMiddleware = [

    ];
}