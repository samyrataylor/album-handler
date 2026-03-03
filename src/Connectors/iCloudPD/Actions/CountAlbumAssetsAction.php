<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\iCloudPD;
use SamyraTaylor\AlbumHandler\Connectors\Shell\Process;
use SamyraTaylor\AlbumHandler\Exceptions\ActionException;
use SamyraTaylor\AlbumHandler\Exceptions\AlbumNotFoundException;
use SamyraTaylor\AlbumHandler\Support\App;

class CountAlbumAssetsAction extends CountLibraryAssetsAction
{
    public static bool $throwException = true;

    protected function parseArguments(array $arguments): array
    {
        return [
            'album' => $arguments[0],
        ];
    }

    protected function build(Client $client): Client
    {
        return $client->dryRun()
                      ->directory($this->user->albumDirectory)
                      ->album($this->getArgument('album', ''));
    }

    protected function pipeProcessOutput(): string
    {
        return App::config()->path('count_album_assets_' . $this->getArgument('album'), $this->user->userDirectory);
    }

    /**
     * @throws AlbumNotFoundException
     * @throws ActionException
     */
    protected function beforeRun(Process $process): Process
    {
        $albums = iCloudPD::user($this->user)->listAlbums();

        if (!in_array($this->getArgument('album'), $albums)) {
            $process->cancel();

            if (static::$throwException) {
                throw new AlbumNotFoundException($this->getArgument('album'));
            }
        }

        return $process;
    }
}