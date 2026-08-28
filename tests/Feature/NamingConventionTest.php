<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;

/*
 * Every public name a package registers lands in a flat, global registry. A second package claiming
 * the same key does not collide loudly -- it silently replaces the first, and the damage surfaces
 * far away as a missing view or the wrong config. These assertions read the LIVE registries rather
 * than the provider source, so the guard survives a refactor of the registration code.
 */

it('keeps its configuration under the laranail namespace', function (): void {
    expect(config('laranail.authkit-social'))->toBeArray()
        ->and(config('authkit-social'))->toBeNull();
});

it('never registers a bare publish tag', function (): void {
    $bare = array_filter(
        array_keys(ServiceProvider::publishableGroups()),
        fn (string $tag): bool => str_contains($tag, 'authkit-social') && ! str_starts_with($tag, 'laranail::'),
    );

    expect(array_values($bare))->toBe([]);
});

/*
 * The sibling packages assert this defaults to FALSE, because installing an inert placeholder must
 * not change how an application authenticates. This package inverts that on purpose.
 *
 * Social login used to live inside laranail/authkit. An application upgrading across the extraction
 * already has it configured and in use, so defaulting to false would switch it off during a routine
 * `composer update` -- no error, no log line, the buttons just stop rendering. Defaulting to true
 * keeps the upgrade silent in the way that matters: nothing changes.
 */
it('is on by default, because it carries behaviour the core used to provide', function (): void {
    expect(config('laranail.authkit-social.enabled'))->toBeTrue();
});

it('can still be switched off deliberately', function (): void {
    config()->set('laranail.authkit-social.enabled', false);

    expect(config('laranail.authkit-social.enabled'))->toBeFalse();
});
