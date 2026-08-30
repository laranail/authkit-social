<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Simtabi\Laranail\AuthKit\Contracts\SocialIdentityProviderInterface;

interface UnlinkSocialAccountInterface
{
    /**
     * Remove a linked provider from a user.
     *
     * Returns false when removing it would leave the user unable to sign in at all — a social-only
     * account has no password, so its last linked provider is its entire means of access.
     */
    public function execute(Authenticatable $user, SocialIdentityProviderInterface $provider): bool;
}
