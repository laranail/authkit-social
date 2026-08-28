<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Simtabi\Laranail\AuthKit\Social\Models\Social;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Simtabi\Laranail\AuthKit\Social\Enums\SocialProvider;

interface CreateSocialAccountActionInterface
{
    public function execute(Authenticatable $authenticatable, SocialProvider $provider, SocialiteUser $socialUser): Social;
}
