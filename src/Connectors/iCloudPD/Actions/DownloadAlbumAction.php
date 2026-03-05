<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\Size;
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
        $album = $this->getArgument('album');

        $dir = implode(DIRECTORY_SEPARATOR, [
            $this->user->albumDirectory,
            str_replace(' ', '_', preg_replace('/\/\\\\/', '', $album)),
        ]);

        return $client->directory($dir)
                      ->album($this->getArgument('album'))
                      ->xmpSidecar()
                      ->size(Size::Original)
                      ->size(Size::Adjusted)
                      ->size(Size::Alternative);
    }

    protected function pipeProcessOutput(): string
    {
        return App::config()->path(
            'download_album_' . $this->getArgument('album'),
            $this->user->userDirectory
        );
    }


}