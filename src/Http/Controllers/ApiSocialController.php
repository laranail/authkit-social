<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Http\Controllers;

use Simtabi\Laranail\AuthKit\Support\AuthKit;

class ApiSocialController extends AbstractApiSocialController
{
    protected function guard(): string
    {
        return AuthKit::guard();
    }
}
