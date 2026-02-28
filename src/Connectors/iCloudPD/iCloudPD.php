<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD;

use SamyraTaylor\AlbumHandler\Connectors\Shell\BackgroundProcess;
use SamyraTaylor\AlbumHandler\Connectors\Shell\ExecutedProcess;
use SamyraTaylor\AlbumHandler\Connectors\Shell\Process;
use SamyraTaylor\AlbumHandler\Support\App;
use SamyraTaylor\AlbumHandler\Support\Config;

class iCloudPD
{
    public Client $client;
    public readonly Client $baseClient;

    public function __construct(?Client $client = null)
    {
        $client ??= new Client();

        if ($username = App::config()->get('icloudpd.auth.username')) {
            dump($username);
            $client->username($username);
        }

        if ($password = App::config()->get('icloudpd.auth.password')) {
            dump($password);
            $client->password($password);
        }

        $this->baseClient = $client;
        $this->client = clone $client;
    }

    public function version(): string
    {
        $this->newClient()->version();

        preg_match('/^version:(.*?),/', array_last($this->run()->output()), $matches);

        return $matches[1] ?? '';
    }

    public function newClient(): Client
    {
        $this->client = clone $this->baseClient;

        return $this->client;
    }

    public function run(): ExecutedProcess
    {
        return new Process($this->client->build())->getExecutedProcess();
    }

    public function authOnly(): bool
    {
        $this->newClient()->authOnly();

        return array_any(
            array: $this->run()->output(),
            callback: fn($line) => str_contains($line, 'Authentication completed successfully')
        );
    }

    public function download() {}

    public function listLibraries(): array
    {
        $this->newClient()->listLibraries();

        return array_values(
            array_filter(
                $this->run()->output,
                fn(string $item) => !empty($item) && !str_starts_with($item, date('Y-m-d'))
            )
        );
    }

    public function listAlbums(): array
    {
        $this->newClient()->listAlbums();

        return array_values(
            array_filter(
                $this->run()->output(),
                fn(string $item) => !empty($item) &&
                    !str_starts_with($item, date('Y-m-d')) &&
                    !in_array(
                        $item,
                        [
                            'Albums:',
                            'Recently Deleted',
                            'Hidden',
                            'Live',
                        ]
                    )
            )
        );
    }

    public function getAssetCount(?string $album = null): int
    {
        $output = Config::userPath() . '/' . rand() . '.out';

        $this->newClient()->dryRun()->directory('.');

        if ($album) {
            $this->client->album($album);
        }

        $bg = $this->runInBackground($output);

        do {
            usleep(300000);

            $filtered = array_values(
                array_filter(
                    $bg->output(),
                    fn(string $item) => str_contains($item, 'original')
                )
            );

        } while (count($filtered) === 0);

        $bg->kill();

        return (int)preg_replace('/^.* Downloading ([0-9]+) .*/', '\1', $filtered[0]);
    }

    public function runInBackground(string $output = '/dev/null'): BackgroundProcess
    {
        return new BackgroundProcess($this->client->build())->pipeOutput($output)->run();
    }


}