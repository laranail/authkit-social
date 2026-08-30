<?php

declare(strict_types=1);

use Workbench\App\Models\User;
use Simtabi\Laranail\AuthKit\Social\Models\Social;
use Simtabi\Laranail\AuthKit\Support\IdentityProvider;
use Simtabi\Laranail\AuthKit\Social\Enums\SocialProvider;
use Simtabi\Laranail\AuthKit\Contracts\IdentityProviderRegistryInterface;

function storeLink(string $provider): Social
{
    $user = User::factory()->create();

    return Social::query()->create([
        'socialable_type' => $user::class,
        'socialable_id'   => $user->getKey(),
        'provider'        => $provider,
        'provider_id'     => 'pid-' . $provider,
        'email'           => $user->email,
    ]);
}

it('reads a built-in provider back as its enum case', function (): void {
    storeLink('google');

    expect(Social::first()->provider)->toBe(SocialProvider::GOOGLE);
});

it('reads a registry-contributed provider back instead of throwing', function (): void {
    // Casting straight to the enum made this a ValueError, so a sub-package could register a
    // provider, render its button and bind its driver -- and then the first person to sign in with
    // it made the row unreadable. The seam worked right up to the point of being used.
    app(IdentityProviderRegistryInterface::class)->register(new IdentityProvider(
        slug: 'okta', label: 'Okta', assertsEmailVerified: true,
    ));
    storeLink('okta');

    expect(Social::first()->provider)->toBeInstanceOf(IdentityProvider::class)
        ->and(Social::first()->provider->slug())->toBe('okta');
});

it('reads an uninstalled provider back as its slug rather than making the row unreadable', function (): void {
    // A sub-package can be removed while its rows remain. The link should still be listable and
    // unlinkable, not poison for the whole account.
    storeLink('retired-idp');

    expect(Social::first()->provider)->toBe('retired-idp');
});

it('stores a provider object as its slug', function (): void {
    $user = User::factory()->create();
    Social::query()->create([
        'socialable_type' => $user::class,
        'socialable_id'   => $user->getKey(),
        'provider'        => SocialProvider::GOOGLE,
        'provider_id'     => 'obj-1',
        'email'           => $user->email,
    ]);

    expect(Social::query()->where('provider', 'google')->count())->toBe(1);
});
