<?php

use SamyraTaylor\AlbumHandler\Support\App;

return [
    'cache' => [
        'enabled' => App::env('CACHE_ENABLED', true),
        'length'  => App::env('CACHE_LENGTH', '1 day'),
    ],

    'users' => [
        'enabled'    => App::env('MULTI_USER', false),
        'cookies'    => App::env('USER_COOKIES', false),
        'useAliases' => App::env('ALIAS_USERS', true),
        'infoFile'   => App::env('USER_INFO', 'user.json'),
        'default'    => App::env('DEFAULT_USER'),
    ],

    'commandNamespaceMap' => [
        'src/Commands' => '\\SamyraTaylor\\AlbumHandler\\Commands\\',
    ],

    'timezone' => App::env('APP_TIMEZONE', 'UTC'),
];
