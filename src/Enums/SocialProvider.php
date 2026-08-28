<?php

declare(strict_types=1);

namespace Simtabi\Laranail\AuthKit\Social\Enums;

use Simtabi\Laranail\Enumerator\Attributes\Label;
use Simtabi\Laranail\Enumerator\Contracts\Enumerator;
use Simtabi\Laranail\Enumerator\Concerns\HasEnumerator;

enum SocialProvider: string implements Enumerator
{
    use HasEnumerator;

    #[Label('Google')]
    case GOOGLE = 'google';

    #[Label('Apple')]
    case APPLE = 'apple';

    #[Label('X (Twitter)')]
    case TWITTER = 'twitter';

    #[Label('LinkedIn')]
    case LINKEDIN = 'linkedin';

    #[Label('PayPal')]
    case PAYPAL = 'paypal';

    /**
     * Whether this provider asserts that it verified the address it returns.
     *
     * Only a provider that asserts verification may auto-link a social login to an
     * existing account by email address. One that does not must never link, because
     * anyone able to register an account carrying someone else's address would
     * otherwise take over that account.
     *
     * Every provider currently shipped asserts it. The method is the seam: adding a
     * provider that does not is a single arm here, and it then cannot link without
     * anything else changing.
     */
    public function assertsEmailVerified(): bool
    {
        return match ($this) {
            self::GOOGLE, self::APPLE, self::LINKEDIN, self::PAYPAL, self::TWITTER => true,
        };
    }

    /**
     * Whether this provider's raw payload asserts the address was verified.
     *
     * The claim differs in name and in shape, so each provider reads its own. The
     * OIDC-style providers return a boolean `email_verified`, which may arrive as a
     * real boolean or as the string "true"/"false" — Apple routinely sends the string
     * form. X instead returns the confirmed address itself as `confirmed_email` and
     * omits it entirely when the address is unconfirmed, so its presence as a valid
     * address is the assertion.
     *
     * Apple may return a per-app relay address on `@privaterelay.appleid.com`. Apple
     * verifies those, so they are trusted here; they simply will not match a local
     * account, so in practice they provision rather than link.
     *
     * The match is deliberately exhaustive: a new case on this enum fails loudly
     * here rather than defaulting to either answer.
     *
     * @param  array<string, mixed>  $rawUser
     */
    public function hasVerifiedEmail(array $rawUser): bool
    {
        return match ($this) {
            self::GOOGLE, self::APPLE, self::LINKEDIN, self::PAYPAL => filter_var(
                $rawUser['email_verified'] ?? false,
                FILTER_VALIDATE_BOOLEAN,
            ),
            self::TWITTER => is_string($rawUser['confirmed_email'] ?? null)
                && filter_var($rawUser['confirmed_email'], FILTER_VALIDATE_EMAIL) !== false,
        };
    }
}
