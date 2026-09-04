<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Simtabi\Laranail\AuthKit\Social\Models\Social;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Simtabi\Laranail\AuthKit\Contracts\SocialIdentityProviderInterface;
use Simtabi\Laranail\AuthKit\Social\Contracts\CreateSocialAccountActionInterface;

class CreateSocialAccountAction implements CreateSocialAccountActionInterface
{
    public function execute(Authenticatable $authenticatable, SocialIdentityProviderInterface $provider, SocialiteUser $socialUser): Social
    {
        return Social::create([
            'socialable_type' => get_class($authenticatable),
            'socialable_id'   => $authenticatable->getAuthIdentifier(),
            'provider'        => $provider,
            'provider_id'     => $socialUser->getId(),
            'name'            => $socialUser->getName(),
            'nickname'        => $socialUser->getNickname(),
            'email'           => $socialUser->getEmail(),
            'avatar_url'      => $socialUser->getAvatar(),
            'token'           => $socialUser->token,
            'refresh_token'   => $socialUser->refreshToken,
            'expires_at'      => $socialUser->expiresIn
                ? now()->addSeconds($socialUser->expiresIn)
                : null,
        ]);
    }
}
