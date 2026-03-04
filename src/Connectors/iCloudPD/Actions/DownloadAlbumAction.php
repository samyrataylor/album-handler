<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Support\App;

class DownloadAlbumAction extends DownloadLibraryAction
{
    protected function parseArguments(array $arguments): array
    {
        return [
            'album' => $arguments[0],
        ];
    }

    protected function build(Client $client): Client
    {
        return $client->directory($this->user->albumDirectory)
                      ->album($this->getArgument('album'));
    }

    protected function pipeProcessOutput(): string
    {
        return App::config()->path(
            'download_album_' . $this->getArgument('album'),
            $this->user->userDirectory
        );
    }


}