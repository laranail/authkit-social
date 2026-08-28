<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | DEFAULTS TRUE, UNLIKE EVERY OTHER AUTHKIT SIBLING, AND THE DIFFERENCE IS
    | DELIBERATE. authkit-social, -ldap, -oauth and -tenancy default to false
    | because they are inert placeholders: installing one must not change how an
    | application authenticates.
    |
    | This package is not that. It carries social login that used to live inside
    | laranail/authkit, so an application upgrading across the extraction has it
    | already configured and already in use. Defaulting to false would switch
    | social login off during a routine `composer update`, with no error and
    | nothing in the logs -- the buttons would simply stop appearing.
    |
    | Set AUTHKIT_SOCIAL_ENABLED=false to turn it off deliberately.
    |
    */

    'enabled' => (bool) env(key: 'AUTHKIT_SOCIAL_ENABLED', default: true),

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    |
    | Moved here from laranail.authkit.social when social login left the core.
    |
    | THE ENV VARIABLE NAMES ARE UNCHANGED ON PURPOSE. They sit in deployed .env
    | files; renaming them would break every existing installation silently, with
    | credentials simply resolving to null. Only the config KEY moved.
    |
    | SocialServiceProvider copies each block into Laravel's own `services.*`,
    | which is where Socialite reads credentials from.
    |
    */

    'google' => [
        'client_id'     => env(key: 'AUTHKIT_GOOGLE_CLIENT_ID'),
        'client_secret' => env(key: 'AUTHKIT_GOOGLE_CLIENT_SECRET'),
        'redirect'      => env(key: 'AUTHKIT_GOOGLE_REDIRECT'),
        'scopes'        => ['openid', 'profile', 'email'],
    ],

    /*
     * Apple's client_secret is not a static string: it is a short-lived ES256 JWT signed
     * with the .p8 key from your Apple developer account, and Apple caps its lifetime at
     * six months. Generate it out of band and rotate it before it expires, or every Apple
     * sign-in starts failing on a date nothing in this repository records.
     *
     * The client_id is the Services ID, not the App ID.
     */
    'apple' => [
        'client_id'     => env(key: 'AUTHKIT_APPLE_CLIENT_ID'),
        'client_secret' => env(key: 'AUTHKIT_APPLE_CLIENT_SECRET'),
        'redirect'      => env(key: 'AUTHKIT_APPLE_REDIRECT'),
        'scopes'        => ['name', 'email'],
    ],

    'twitter' => [
        'client_id'     => env(key: 'AUTHKIT_TWITTER_CLIENT_ID'),
        'client_secret' => env(key: 'AUTHKIT_TWITTER_CLIENT_SECRET'),
        'redirect'      => env(key: 'AUTHKIT_TWITTER_REDIRECT'),
        'scopes'        => ['users.read', 'users.email', 'tweet.read'],
    ],

    'linkedin' => [
        'client_id'     => env(key: 'AUTHKIT_LINKEDIN_CLIENT_ID'),
        'client_secret' => env(key: 'AUTHKIT_LINKEDIN_CLIENT_SECRET'),
        'redirect'      => env(key: 'AUTHKIT_LINKEDIN_REDIRECT'),
        'scopes'        => ['openid', 'profile', 'email'],
    ],

    'paypal' => [
        'client_id'     => env(key: 'AUTHKIT_PAYPAL_CLIENT_ID'),
        'client_secret' => env(key: 'AUTHKIT_PAYPAL_CLIENT_SECRET'),
        'redirect'      => env(key: 'AUTHKIT_PAYPAL_REDIRECT'),
        'sandbox_mode'  => (bool) env(key: 'AUTHKIT_PAYPAL_SANDBOX_MODE', default: true),
        'scopes'        => ['openid', 'profile', 'email'],
    ],

];
