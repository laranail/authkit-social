# Changelog

All notable changes to `laranail/authkit-social` are documented here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project
adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Removed

- **`SocialProvider::FACEBOOK`, and its config block.** Breaking for anyone referencing the case or
  setting `AUTHKIT_FACEBOOK_*`. Facebook returns no email-verification flag — only an inference from
  its documentation — and an address that cannot be shown to be verified must never auto-link, because
  anyone able to register an account carrying someone else's address would otherwise take over that
  account. It was previously listed as supported while being unable to authenticate at all.

### Fixed

- **An unknown provider slug returned a 500, not a 404.** `SocialRedirectAction` and
  `SocialCallbackAction` passed the route parameter straight to `SocialProvider::from()`, so any
  unrecognised value raised an uncaught `ValueError` on user-controlled input. Both now use
  `tryFrom()` and raise `NotFoundHttpException`.

- **X (Twitter) could never sign in.** The verification check only ever read `email_verified`, a claim
  X does not use. X returns the confirmed address itself as `confirmed_email` — Socialite already
  requests it via the `users.email` scope and `user.fields` — so every X login was treated as
  unverified. It is now trusted through its own claim.

### Changed

- **The email-trust policy moved onto `SocialProvider`.** What was a hardcoded allow-list inside a
  private method is now `assertsEmailVerified()` (does this provider assert verification at all) and
  `hasVerifiedEmail()` (does this payload satisfy that provider's own claim). Both matches are
  exhaustive, so adding a case forces an explicit decision instead of silently inheriting one, and
  adding a provider is a single arm rather than an edit to the resolver.

  Behaviour for Google, LinkedIn, and PayPal is unchanged. A test now pins that a claim carrying the
  wrong key for a provider does not count as verification.


### Added

- **Social login, extracted from `laranail/authkit`.** Fifteen classes, the `socials` migration and
  its factory, plus the provider credential block that was `laranail.authkit.social` and is now
  `laranail.authkit-social`. Provider env variable names are unchanged.

  The two abstract controllers gained an explicit
  `use Simtabi\Laranail\AuthKit\Http\Controllers\AbstractAuthController;`. They previously resolved
  it by same-namespace lookup, which the move breaks — silently, because the file still parses.

- Package skeleton: service provider, vendor-scoped config key and publish tag, CI, and the
  naming-convention guard every laranail package carries.

  Unlike its sibling packages, this one is **on by default**
  (`AUTHKIT_SOCIAL_ENABLED`, default `true`). The others are inert placeholders whose installation
  must not change behaviour; this package takes over social login that `laranail/authkit` used to
  provide, so an application upgrading across the extraction already has it configured. Defaulting
  off would disable social login during a routine `composer update`, with nothing reported.

- CI actions pinned to commit SHAs, per the org standard. The four existing authkit siblings still
  float on `@v5` — a separate cleanup.
