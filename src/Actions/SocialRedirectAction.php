<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Actions;

use Illuminate\Http\Request;
use Simtabi\Laranail\AuthKit\Social\Enums\SocialProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Simtabi\Laranail\AuthKit\Social\Support\SocialRedirectResult;
use Simtabi\Laranail\AuthKit\Social\Contracts\SocialRedirectActionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Simtabi\Laranail\AuthKit\Social\Support\ResolvesIdentityProvider;

class SocialRedirectAction implements SocialRedirectActionInterface
{
    use ResolvesIdentityProvider;

    public function __construct(
        private SocialiteFactory $socialite,
    ) {}

    public function execute(Request $request): SocialRedirectResult
    {
        $provider = $this->resolveProvider(request: $request);

        return new SocialRedirectResult(
            url: $this->socialite->driver($provider->slug())->redirect()->getTargetUrl(),
        );
    }
}
