<?php

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\AlignRaw;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\Domain;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\FileMatchPolicy;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\LivePhotoFilenamePolicy;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\LogLevel;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\MFAProvider;
use SamyraTaylor\AlbumHandler\Support\App;

return [
    'allowDestructiveActions' => App::env('ALLOW_DESTRUCTIVE_ACTIONS', false),

    'auth' => [
        'username' => App::env('ICLOUDPD_USERNAME'),
        'password' => App::env('ICLOUDPD_PASSWORD'),
    ],

    'install_path' => null,

    'name' => 'icloudpd',

    'defaults' => [
        'cookieDirectory'            => '~/.pyicloud',
        'library'                    => 'PrimarySync',
        'folderStructure'            => '{:%Y/%m/%d}',
        'smtpHost'                   => 'smtp.gmail.com',
        'smtpPort'                   => 587,
        'livePhotoMovFilenamePolicy' => LivePhotoFilenamePolicy::Suffix,
        'alignRaw'                   => AlignRaw::AsIs,
        'fileMatchPolicy'            => FileMatchPolicy::NameSizeDedupWithSuffix,
        'logLevel'                   => LogLevel::Debug,
        'domain'                     => Domain::Com,
        'mfaProvider'                => MFAProvider::Console,
    ],
];