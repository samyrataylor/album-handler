<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options;

use SamyraTaylor\AlbumHandler\Contracts\Choice;

enum LivePhotoFilenamePolicy: string implements Choice
{
    case Suffix = 'suffix';
    case Original = 'original';
}