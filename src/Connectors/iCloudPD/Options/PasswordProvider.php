<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options;

use SamyraTaylor\AlbumHandler\Contracts\Choice;

enum PasswordProvider: string implements Choice
{
    case Console = 'console';
    case Keyring = 'keyring';
    case Parameter = 'parameter';
    case WebUI = 'webui';
}
