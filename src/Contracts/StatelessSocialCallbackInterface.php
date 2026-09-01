<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Contracts;

use Simtabi\Laranail\AuthKit\Contracts\SocialIdentityProviderInterface;
use Simtabi\Laranail\AuthKit\Support\TokenResult;

interface StatelessSocialCallbackInterface
{
    /**
     * Complete a social sign-in for a client with no session and return an API token.
     *
     * Takes an authorization code from the request, not an access token from the client: only the
     * code flow lets this package verify that the credential was issued to *this* application. See
     * StatelessSocialCallback for why the token-exchange shape is deliberately not offered.
     *
     * Returns null when the identity resolves to no user — an unverified or missing email, or an
     * existing account the provider cannot prove ownership of.
     */
    public function execute(SocialIdentityProviderInterface $provider, string $guard, ?string $tokenName = null): ?TokenResult;
}
