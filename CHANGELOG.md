# Changelog

All notable changes to `laranail/authkit-social` are documented here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project
adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
