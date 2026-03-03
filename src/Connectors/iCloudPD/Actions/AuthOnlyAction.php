<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Connectors\Shell\Process;

class AuthOnlyAction extends BaseAction
{
    public static bool $requiresAuth = true;

    public function build(Client $client): Client
    {
        return $client->authOnly();
    }

    public function handle(Process $process): bool
    {
        return array_any(
            array: $process->output(),
            callback: fn($line) => str_contains($line, 'Authentication completed successfully')
        );
    }


}