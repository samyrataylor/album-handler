<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options;

use SamyraTaylor\AlbumHandler\Contracts\Choice;

enum Size: string implements Choice
{
    case Original = 'original';
    case Medium = 'medium';
    case Thumb = 'thumb';
    case Adjusted = 'adjusted';
    case Alternative = 'alternative';


}
