<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Simtabi\Laranail\AuthKit\Social\Contracts\ResolveSocialIdentityInterface;
use Simtabi\Laranail\AuthKit\Social\Contracts\SocialCallbackActionInterface;
use Simtabi\Laranail\AuthKit\Social\Support\ResolvesIdentityProvider;
use Simtabi\Laranail\AuthKit\Support\AuthResult;

class SocialCallbackAction implements SocialCallbackActionInterface
{
    use ResolvesIdentityProvider;

    public function __construct(
        private SocialiteFactory $socialite,
        private ResolveSocialIdentityInterface $resolver,
    ) {}

    public function execute(Request $request, string $guard): AuthResult
    {
        $provider = $this->resolveProvider(request: $request);
        $socialiteUser = $this->socialite->driver($provider->driver())->user();

        $user = $this->resolver->execute(
            provider: $provider,
            socialUser: $socialiteUser,
            guard: $guard,
        );

        if (! $user instanceof Authenticatable) {
            return AuthResult::failed();
        }

        return AuthResult::passed($user);
    }
}
