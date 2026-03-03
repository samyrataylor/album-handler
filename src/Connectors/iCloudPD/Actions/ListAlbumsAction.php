<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Connectors\Shell\Process;

class ListAlbumsAction extends BaseAction
{
    public static bool $requiresAuth = true;

    public function build(Client $client): Client
    {
        return $client->listAlbums();
    }

    public function handle(Process $process): array
    {
        return array_values(
            array_filter(
                $process->output(),
                fn(string $item) => !empty($item) &&
                    !str_starts_with($item, date('Y-m-d')) &&
                    $item !== 'Albums:'
            )
        );
    }
}