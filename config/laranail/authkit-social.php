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

];
