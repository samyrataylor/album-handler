<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options;

use SamyraTaylor\AlbumHandler\Contracts\Choice;

enum AlignRaw: string implements Choice
{
    case AsIs = 'as-is';
    case Original = 'original';
    case Alternative = 'alternative';
}
