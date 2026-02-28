<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options;

use SamyraTaylor\AlbumHandler\Contracts\Choice;

enum FileMatchPolicy: string implements Choice
{
    case NameSizeDedupWithSuffix = 'name-size-dedup-with-suffix';
    case NameID7 = 'name-id7';
}
