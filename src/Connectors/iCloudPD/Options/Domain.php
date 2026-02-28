<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options;

use SamyraTaylor\AlbumHandler\Contracts\Choice;

enum Domain: string implements Choice
{
    case Com = 'com';
    case Cn = 'cn';
}