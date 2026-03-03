<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Connectors\Shell\BackgroundProcess;
use SamyraTaylor\AlbumHandler\Connectors\Shell\Process;
use SamyraTaylor\AlbumHandler\Support\App;

class CountLibraryAssetsAction extends BaseAction
{
    public static RunType $runType = RunType::Background;
    public static bool $requiresAuth = true;

    protected function build(Client $client): Client
    {
        return $client->dryRun()
                      ->directory($this->user->libraryDirectory);
    }

    protected function pipeProcessOutput(): string
    {
        return App::config()->path('count_library_assets', $this->user->userDirectory);
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