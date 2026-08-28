<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Actions;

use Illuminate\Http\Request;
use Simtabi\Laranail\AuthKit\Social\Enums\SocialProvider;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Simtabi\Laranail\AuthKit\Social\Support\SocialRedirectResult;
use Simtabi\Laranail\AuthKit\Social\Contracts\SocialRedirectActionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SocialRedirectAction implements SocialRedirectActionInterface
{
    public function __construct(
        private SocialiteFactory $socialite,
    ) {}

    public function execute(Request $request): SocialRedirectResult
    {
        $provider = SocialProvider::tryFrom((string) $request->route('provider'))
            ?? throw new NotFoundHttpException(message: 'Unknown social provider.');

        return new SocialRedirectResult(
            url: $this->socialite->driver($provider->value)->redirect()->getTargetUrl(),
        );
    }
}
