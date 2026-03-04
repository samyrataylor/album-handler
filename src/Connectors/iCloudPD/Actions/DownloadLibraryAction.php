<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Connectors\Shell\BackgroundProcess;
use SamyraTaylor\AlbumHandler\Connectors\Shell\Process;
use SamyraTaylor\AlbumHandler\Support\App;

class DownloadLibraryAction extends BaseAction
{
    public static RunType $runType = RunType::Background;
    public static bool $requiresAuth = true;

    protected function build(Client $client): Client
    {
        return $client->directory($this->user->libraryDirectory);
    }

    protected function pipeProcessOutput(): string
    {
        return App::config()->path('download_library', $this->user->userDirectory);
    }

    protected function handle(Process $process): ?BackgroundProcess
    {
        if (!$process instanceof BackgroundProcess) {
            return null;
        }

        $this->user->addBackgroundProcess($process);

        return $process;
    }

}