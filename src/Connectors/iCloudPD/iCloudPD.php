<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions\AuthOnlyAction;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions\CountAlbumAssetsAction;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions\ListAlbumsAction;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions\ListLibrariesAction;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions\VersionAction;
use SamyraTaylor\AlbumHandler\Data\User;
use SamyraTaylor\AlbumHandler\Exceptions\ActionException;
use SamyraTaylor\AlbumHandler\Exceptions\AlbumNotFoundException;
use SamyraTaylor\AlbumHandler\Exceptions\MissingCredentialsException;

class iCloudPD
{
    protected(set) User $user;

    /**
     * @throws MissingCredentialsException
     * @throws ActionException
     */
    public function listAlbums(): array
    {
        return new ListAlbumsAction($this->client())->user($this->user)->run();
    }

    public static function user(User $user): static
    {
        return static::make()->setUser($user);
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public static function make(): static
    {
        return new static();
    }

    public function client(): Client
    {
        if (!empty($this->user) && $this->user->exists) {
            return $this->user->client();
        }

        return new Client();
    }

    /**
     * @throws ActionException
     */
    public function listLibraries(): array
    {
        return new ListLibrariesAction($this->client())->user($this->user)->run();
    }

    /**
     * @throws ActionException
     */
    public function version(): string
    {
        return new VersionAction($this->client())->run();
    }

    /**
     * @throws ActionException
     * @throws MissingCredentialsException
     */
    public function authOnly(): bool
    {
        return new AuthOnlyAction($this->client())->user($this->user)->run();
    }

    public function countLibraryAssets(?string $library = null): ?int
    {
        return null;
    }

    /**
     * @throws ActionException
     * @throws MissingCredentialsException
     * @throws AlbumNotFoundException
     */
    public function countAlbumAssets(string $album): ?int
    {
        return new CountAlbumAssetsAction($this->client())->user($this->user)->run($album);
    }

    public function downloadLibrary() {}

    public function downloadAlbum() {}
}