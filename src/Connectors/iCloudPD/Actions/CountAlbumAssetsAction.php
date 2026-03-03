<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\iCloudPD;
use SamyraTaylor\AlbumHandler\Connectors\Shell\BackgroundProcess;
use SamyraTaylor\AlbumHandler\Connectors\Shell\Process;
use SamyraTaylor\AlbumHandler\Exceptions\ActionException;
use SamyraTaylor\AlbumHandler\Exceptions\AlbumNotFoundException;
use SamyraTaylor\AlbumHandler\Support\App;

class CountAlbumAssetsAction extends BaseAction
{
    public static RunType $runType = RunType::Background;
    public static bool $requiresAuth = true;

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

    protected function handle(Process $process): ?int
    {
        if (!$process instanceof BackgroundProcess || $process->cancelled) {
            return null;
        }

        do {
            usleep(300000);

            $filtered = array_values(
                array_filter(
                    $process->output(),
                    fn(string $item) => str_contains($item, 'original')
                )
            );

        } while (count($filtered) === 0);

        $process->kill();

        return (int)preg_replace('/^.* Downloading ([0-9]+) .*/', '\1', $filtered[0]);
    }
}