<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Connectors\Shell\Process;

class ListLibrariesAction extends BaseAction
{
    public static bool $requiresAuth = true;

    public function build(Client $client): Client
    {
        return $client->listLibraries();
    }

    public function handle(Process $process): array
    {
        return $process->output();
    }


}