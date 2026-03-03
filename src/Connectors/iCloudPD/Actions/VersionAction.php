<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Connectors\Shell\Process;

class VersionAction extends BaseAction
{
    public function build(Client $client): Client
    {
        return $client->clearAll()->version();
    }

    public function handle(Process $process): string
    {
        preg_match('/^version:(.*?),/', array_last($process->output()), $matches);

        return $matches[1] ?? '';
    }
}