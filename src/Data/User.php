<?php

namespace SamyraTaylor\AlbumHandler\Data;

use Exception;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Connectors\Shell\BackgroundProcess;
use SamyraTaylor\AlbumHandler\Support\App;
use SamyraTaylor\AlbumHandler\Support\UserProcesses;

class User
{
    protected(set) ?string $name = null;
    protected(set) ?string $libraryDirectory = null;
    protected(set) ?string $albumDirectory = null;
    protected(set) ?string $cookieDirectory = null;
    protected(set) ?string $username = null;
    protected(set) ?string $password = null;
    protected(set) array $includeAlbums = ['*'];
    protected(set) array $excludeAlbums = [];
    protected(set) bool $exists = false;
    public readonly array $json;
    public readonly string $userDirectory;
    public readonly string $userFile;

    public function __construct(
        public readonly string $alias,
    ) {
        $this->userDirectory = App::config()->userPath($alias);
        $this->userFile = implode(DIRECTORY_SEPARATOR, [
            $this->userDirectory,
            App::config()->get('users.infoFile', 'user.json'),
        ]);

        if (file_exists($this->userFile)) {
            $this->exists = true;
            $string = file_get_contents($this->userFile);
            $this->json = json_validate($string) ? json_decode($string, true) : [];
            $this->parse();
        }
    }

    protected function parse(): void
    {
        $this->name = $this->json['name'] ?? null;
        $this->libraryDirectory = $this->parseDirectory($this->json['dir']['library'] ?? null);
        $this->albumDirectory = $this->parseDirectory($this->json['dir']['albums'] ?? null);
        $this->cookieDirectory = $this->parseDirectory($this->json['dir']['cookie'] ?? null);
        $this->username = $this->json['auth']['username'] ?? null;
        $this->password = $this->json['auth']['password'] ?? null;
        $this->includeAlbums = $this->json['albums']['include'] ?? ['*'];
        $this->excludeAlbums = $this->json['albums']['exclude'] ?? [];
    }

    public function shouldDownloadAlbum(string $album): bool
    {
        if(in_array('*', $this->includeAlbums)) {
            return !in_array($album, $this->excludeAlbums);
        }

        return in_array($album, $this->includeAlbums);
    }

    protected function parseDirectory(?string $path = null): ?string
    {
        switch (true) {
            case $path === null:
                return null;

            case $path === '.':
                $path = $this->userDirectory;
                break;

            case $path === '..':
                $path = $this->userDirectory . '..';
                break;

            case str_starts_with($path, './'):
                $path = $this->userDirectory . substr($path, 1);
                break;

            case str_starts_with($path, '../'):
                $path = $this->userDirectory . '../' . substr($path, 1);
                break;

            default:
                break;
        }

        return realpath(str_replace('~', $_ENV['HOME'] ?? '~', $path)) ?: null;
    }

    public static function load(string $name): static
    {
        return new static($name);
    }

    public static function loadOrFail(string $name): static
    {
        $user = new static($name);

        if ($user->exists) {
            return $user;
        }

        throw new Exception('User not found!');
    }

    public function client(): ?Client
    {
        if (!$this->exists) {
            return null;
        }

        $client = new Client();

        if (!empty($this->username)) {
            $client->username($this->username);
        }

        if (!empty($this->password)) {
            $client->password($this->password);
        }

        if (!empty($this->cookieDirectory)) {
            $client->cookieDirectory($this->cookieDirectory);
        }

        return $client;
    }
}