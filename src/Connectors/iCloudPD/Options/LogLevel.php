<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options;

use SamyraTaylor\AlbumHandler\Contracts\Choice;

enum LogLevel: string implements Choice
{
    case Debug = 'debug';
    case Info = 'info';
    case Error = 'error';
}