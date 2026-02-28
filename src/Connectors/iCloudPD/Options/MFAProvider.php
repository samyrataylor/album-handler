<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options;

use SamyraTaylor\AlbumHandler\Contracts\Choice;

enum MFAProvider: string implements Choice
{
    case Console = 'console';
    case WebUI = 'webui';
}
