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

    #[Label('Facebook')]
    case FACEBOOK = 'facebook';

    #[Label('X (Twitter)')]
    case TWITTER = 'twitter';

    #[Label('LinkedIn')]
    case LINKEDIN = 'linkedin';

    #[Label('PayPal')]
    case PAYPAL = 'paypal';

    /**
     * Whether this provider returns a trustworthy `email_verified` claim.
     *
     * Only a provider that asserts verification may auto-link a social login to
     * an existing account by email address. One that does not assert it — or
     * asserts it unreliably — must never link, because anyone able to register
     * an account carrying someone else's address would otherwise take over that
     * account.
     *
     * Adding a case here is a security decision, not a feature toggle: confirm
     * the provider genuinely verifies the address and returns the claim in its
     * raw payload first. The match is deliberately exhaustive, so a new case on
     * this enum fails loudly rather than defaulting to either answer.
     */
    public function assertsVerifiedEmail(): bool
    {
        return match ($this) {
            self::GOOGLE, self::LINKEDIN, self::PAYPAL => true,
            self::FACEBOOK, self::TWITTER => false,
        };
    }
}
