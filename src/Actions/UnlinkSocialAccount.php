<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Simtabi\Laranail\AuthKit\Contracts\SocialIdentityProviderInterface;
use Simtabi\Laranail\AuthKit\Social\Contracts\UnlinkSocialAccountInterface;
use Simtabi\Laranail\AuthKit\Social\Services\SocialAccountService;

class UnlinkSocialAccount implements UnlinkSocialAccountInterface
{
    public function __construct(private readonly SocialAccountService $accounts) {}

    public function execute(Authenticatable $user, SocialIdentityProviderInterface $provider): bool
    {
        return $this->accounts->unlink(user: $user, provider: $provider);
    }
}
