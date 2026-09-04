<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Actions;

use Illuminate\Support\Str;
use Laravel\Socialite\AbstractUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Simtabi\Laranail\AuthKit\Social\Models\Social;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Simtabi\Laranail\AuthKit\Support\UserModelResolver;
use Simtabi\Laranail\AuthKit\Contracts\SocialIdentityProviderInterface;
use Simtabi\Laranail\AuthKit\Social\Contracts\ResolveSocialIdentityInterface;
use Simtabi\Laranail\AuthKit\Social\Contracts\CreateSocialAccountActionInterface;

class ResolveSocialIdentity implements ResolveSocialIdentityInterface
{
    public function __construct(
        private CreateSocialAccountActionInterface $createSocialAccount,
    ) {}

    public function execute(SocialIdentityProviderInterface $provider, SocialiteUser $socialUser, string $guard): ?Authenticatable
    {
        $social = Social::query()
            ->where('provider', $provider->slug())
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($social !== null) {
            $social->update([
                'token'         => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken,
                'expires_at'    => $socialUser->expiresIn
                    ? now()->addSeconds($socialUser->expiresIn)
                    : null,
            ]);

            return $social->socialable;
        }

        $userModel = UserModelResolver::resolve(guard: $guard);

        if (auth()->check()) {
            $this->createSocialAccount->execute(
                authenticatable: auth()->user(),
                provider: $provider,
                socialUser: $socialUser,
            );

            return auth()->user();
        }

        $email = $this->normalizedVerifiedEmail($provider, $socialUser);

        if ($email !== null && ($existingUser = $this->findUserByEmail($userModel, $email)) !== null) {
            $this->createSocialAccount->execute(
                authenticatable: $existingUser,
                provider: $provider,
                socialUser: $socialUser,
            );

            return $existingUser;
        }

        if ($email === null || $this->findUserByEmail($userModel, $email) !== null) {
            return null;
        }

        $user = $this->createUser($userModel, $socialUser, $email);

        $this->createSocialAccount->execute(
            authenticatable: $user,
            provider: $provider,
            socialUser: $socialUser,
        );

        return $user;
    }

    private function normalizedVerifiedEmail(SocialIdentityProviderInterface $provider, SocialiteUser $socialUser): ?string
    {
        $email = $socialUser->getEmail();

        if ($email === null || ! $this->emailIsVerified($provider, $socialUser)) {
            return null;
        }

        return Str::lower($email);
    }

    private function emailIsVerified(SocialIdentityProviderInterface $provider, SocialiteUser $socialUser): bool
    {
        // hasVerifiedEmail() is the whole question: a provider that asserts nothing answers
        // false there whatever the payload claims, whether it is a built-in enum case or one
        // contributed by a sub-package.
        $rawUser = $socialUser instanceof AbstractUser
            ? $socialUser->getRaw()
            : [];

        return is_array($rawUser) && $provider->hasVerifiedEmail($rawUser);
    }

    private function findUserByEmail(string $userModel, ?string $email): ?Authenticatable
    {
        if ($email === null) {
            return null;
        }

        return $userModel::query()->where('email', $email)->first();
    }

    private function createUser(string $userModel, SocialiteUser $socialUser, string $email): Authenticatable
    {
        /** @var Model&Authenticatable $user */
        $user = new $userModel;
        $user->forceFill([
            'name'              => $socialUser->getName() ?? $socialUser->getNickname() ?? '',
            'email'             => $email,
            'email_verified_at' => now(),
            'password'          => Hash::make(Str::random(32)),
        ]);
        $user->save();

        return $user;
    }
}
