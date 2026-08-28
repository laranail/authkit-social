<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Contracts;

use Illuminate\Http\Request;
use Simtabi\Laranail\AuthKit\Social\Support\SocialRedirectResult;

interface SocialRedirectActionInterface
{
    public function execute(Request $request): SocialRedirectResult;
}
