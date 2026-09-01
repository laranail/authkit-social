<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Simtabi\Laranail\AuthKit\Contracts\SocialIdentityProviderInterface;

interface ResolveSocialIdentityInterface
{
    public function execute(SocialIdentityProviderInterface $provider, SocialiteUser $socialUser, string $guard): ?Authenticatable;
}
