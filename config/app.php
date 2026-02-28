<?php

use SamyraTaylor\AlbumHandler\Support\App;

return [
    'timezone' => App::env('APP_TIMEZONE', 'UTC'),

    'commandNamespaceMap' => [
        'src/Commands' => '\\SamyraTaylor\\AlbumHandler\\Commands\\',
    ],
];
