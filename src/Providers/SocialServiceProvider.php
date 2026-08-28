<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Providers;

use Illuminate\Support\Facades\Event;
use Simtabi\Laranail\AuthKit\Social\Actions;
use Simtabi\Laranail\AuthKit\Social\Contracts;
use Simtabi\Laranail\AuthKit\Social\Services;
use Simtabi\Laranail\Package\Tools\Package;
use Simtabi\Laranail\Package\Tools\Providers\PackageServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

/**
 * Social login for laranail/authkit.
 *
 * Everything here moved out of AuthKitServiceProvider when social login left the core. Extends the
 * core through its published seams and never edits it. Every public name is vendor-scoped: the
 * config key is laranail.authkit-social and publish tags are laranail::authkit-social-*, because
 * Laravel keeps these in flat global maps where a second package claiming the same key silently
 * replaces the first.
 *
 * THE ENABLED GATE DEFAULTS TRUE HERE, unlike its sibling packages. authkit-sso, -ldap, -oauth and
 * -tenancy default false because they are inert placeholders. This package took over behaviour the
 * core used to provide, so an application upgrading across the extraction already has social login
 * configured and in use -- defaulting false would switch it off during a routine `composer update`
 * with no error anywhere.
 */
class SocialServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laranail/authkit-social')
            ->publish(
                paths: ['config/laranail/authkit-social.php' => config_path(path: 'laranail/authkit-social.php')],
                tag: 'laranail::authkit-social-config',
            )
            /*
             * The core published this same tag until the extraction. It drops it in the same change,
             * so exactly one package owns it -- NamingConventionTest cannot catch a duplicate,
             * because it only rejects tags missing the laranail:: prefix.
             */
            ->publish(
                paths: ['database/migrations/social' => database_path(path: 'migrations')],
                tag: 'laranail::authkit-social-migrations',
            );
    }

    public function packageRegistered(): void
    {
        $this->mergeConfigFrom(path: $this->packagePath('config/laranail/authkit-social.php'), key: 'laranail.authkit-social');

        $this->app->bind(abstract: Contracts\SocialRedirectActionInterface::class, concrete: Actions\SocialRedirectAction::class);
        $this->app->bind(abstract: Contracts\SocialCallbackActionInterface::class, concrete: Actions\SocialCallbackAction::class);
        $this->app->bind(abstract: Contracts\CreateSocialAccountActionInterface::class, concrete: Actions\CreateSocialAccountAction::class);
        $this->app->bind(abstract: Contracts\ResolveSocialIdentityInterface::class, concrete: Actions\ResolveSocialIdentity::class);
    }

    public function packageBooted(): void
    {
        // `default: true` matches the shipped config. Reading false when the key is absent would
        // disable social login for anyone who has not published the config file.
        if (! config(key: 'laranail.authkit-social.enabled', default: true)) {
            return;
        }

        $this->publishProviderCredentials();
        $this->registerPayPalProvider();
    }

    /**
     * Socialite reads credentials from Laravel's own `services.*`, not from this package's config,
     * so each provider block is copied across.
     *
     * This was called registerConfig() in the core -- a generic name for a social-only method, which
     * is exactly why it was easy to leave behind during the extraction.
     */
    private function publishProviderCredentials(): void
    {
        foreach (config(key: 'laranail.authkit-social', default: []) as $provider => $providerConfig) {
            if ($provider !== 'enabled' && is_array(value: $providerConfig)) {
                config()->set(key: "services.{$provider}", value: $providerConfig);
            }
        }
    }

    private function registerPayPalProvider(): void
    {
        Event::listen(
            events: SocialiteWasCalled::class,
            listener: function (SocialiteWasCalled $event): void {
                $event->extendSocialite(
                    providerName: 'paypal',
                    providerClass: Services\PayPalSocialProvider::class,
                );
            },
        );
    }
}
