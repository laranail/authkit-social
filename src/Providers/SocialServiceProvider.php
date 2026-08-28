<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Providers;

use Simtabi\Laranail\Package\Tools\Package;
use Simtabi\Laranail\Package\Tools\Providers\PackageServiceProvider;

/**
 * Social login for laranail/authkit.
 *
 * Extends the core through its published seams and never edits it. Every public name registered
 * here is vendor-scoped: the config key is laranail.authkit-social and publish tags are
 * laranail::authkit-social-*, because Laravel keeps these in flat global maps where a second
 * package claiming the same key silently replaces the first.
 *
 * THE ENABLED GATE DEFAULTS TRUE HERE, unlike its sibling packages. authkit-social, -ldap, -oauth and
 * -tenancy default false because they are inert placeholders. This package carries social login
 * that used to live in the core, so an application upgrading across the extraction already has it
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
            );
    }

    public function packageRegistered(): void
    {
        $this->mergeConfigFrom(path: $this->packagePath('config/laranail/authkit-social.php'), key: 'laranail.authkit-social');
    }

    public function packageBooted(): void
    {
        // `default: true` matches the shipped config. Reading `false` here when the key is absent
        // would disable social login for anyone who has not published the config file.
        if (! config(key: 'laranail.authkit-social.enabled', default: true)) {
            return;
        }

        // Registrations land here in the extraction commit.
    }
}
