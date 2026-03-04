<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\AlignRaw;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\Domain;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\FileMatchPolicy;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\LivePhotoFilenamePolicy;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\LivePhotoSize;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\LogLevel;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\MFAProvider;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\PasswordProvider;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\PasswordProviderList;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\Size;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options\SizeList;
use SamyraTaylor\AlbumHandler\Contracts\iCloudPDClient;
use SamyraTaylor\AlbumHandler\Support\App;
use SamyraTaylor\AlbumHandler\Support\ParameterBuilder;

class Client implements iCloudPDClient
{
    public const array PARAMETERS = [
        'directory'               => '--directory',
        'authOnly'                => '--auth-only',
        'cookieDirectory'         => '--cookie-directory',
        'size'                    => '--size',
        'livePhotoSize'           => '--live-photo-size',
        'recent'                  => '--recent',
        'untilFound'              => '--until-found',
        'album'                   => '--album',
        'listAlbums'              => '--list-albums',
        'library'                 => '--library',
        'listLibraries'           => '--list-libraries',
        'skipVideos'              => '--skip-videos',
        'skipLivePhotos'          => '--skip-live-photos',
        'xmpSidecar'              => '--xmp-sidecar',
        'forceSize'               => '--force-size',
        'autoDelete'              => '--auto-delete',
        'folderStructure'         => '--folder-structure',
        'setExifDatetime'         => '--set-exif-datetime',
        'smtpUsername'            => '--smtp-username',
        'smtpPassword'            => '--smtp-password',
        'smtpHost'                => '--smtp-host',
        'smtpPort'                => '--smtp-port',
        'smtpNoTLS'               => '--smtp-no-tls',
        'notificationEmail'       => '--notification-email',
        'notificationEmailFrom'   => '--notification-email-from',
        'notificationScript'      => '--notification-script',
        'keepRecentDays'          => '--keep-icloud-recent-days',
        'dryRun'                  => '--dry-run',
        'keepUnicodeInFilenames'  => '--keep-unicode-in-filenames',
        'livePhotoFilenamePolicy' => '--live-photo-mov-filename-policy',
        'alignRaw'                => '--align-raw',
        'fileMatchPolicy'         => '--file-match-policy',
        'skipCreatedBefore'       => '--skip-created-before',
        'skipCreatedAfter'        => '--skip-created-after',
        'skipPhotos'              => '--skip-photos',
        'username'                => '--username',
        'password'                => '--password',
        'version'                 => '--version',
        'useOSLocale'             => '--use-os-locale',
        'onlyPrintFilenames'      => '--only-print-filenames',
        'logLevel'                => '--log-level',
        'noProgressBar'           => '--no-progress-bar',
        'domain'                  => '--domain',
        'watchWithInterval'       => '--watch-with-interval',
        'passwordProvider'        => '--password-provider',
        'mfaProvider'             => '--mfa-provider',
    ];

    public const array DESTRUCTIVE_ACTIONS = [
        'autoDelete',
        'keepRecentDays',
    ];

    protected(set) bool $allowDestructiveActions;
    protected(set) string $name;
    protected(set) ?string $installPath;
    protected(set) string $directory;
    protected(set) bool $authOnly;
    protected(set) string $cookieDirectory;
    protected(set) SizeList $size;
    protected(set) LivePhotoSize $livePhotoSize;
    protected(set) int $recent;
    protected(set) int $untilFound;
    protected(set) array $album;
    protected(set) bool $listAlbums;
    protected(set) string $library;
    protected(set) bool $listLibraries;
    protected(set) bool $skipVideos;
    protected(set) bool $skipLivePhotos;
    protected(set) bool $xmpSidecar;
    protected(set) bool $forceSize;
    protected(set) bool $autoDelete;
    protected(set) string $folderStructure;
    protected(set) bool $setExifDatetime;
    protected(set) string $smtpUsername;
    protected(set) string $smtpPassword;
    protected(set) string $smtpHost;
    protected(set) int $smtpPort;
    protected(set) bool $smtpNoTLS;
    protected(set) string $notificationEmail;
    protected(set) string $notificationEmailFrom;
    protected(set) string $notificationScript;
    protected(set) int $keepRecentDays;
    protected(set) bool $dryRun;
    protected(set) bool $keepUnicodeInFilenames;
    protected(set) LivePhotoFilenamePolicy $livePhotoFilenamePolicy;
    protected(set) AlignRaw $alignRaw;
    protected(set) FileMatchPolicy $fileMatchPolicy;
    protected(set) string $skipCreatedBefore;
    protected(set) string $skipCreatedAfter;
    protected(set) bool $skipPhotos;
    protected(set) string $username;
    protected(set) string $password;
    protected(set) bool $version;
    protected(set) bool $useOSLocale;
    protected(set) bool $onlyPrintFilenames;
    protected(set) LogLevel $logLevel;
    protected(set) bool $noProgressBar;
    protected(set) Domain $domain;
    protected(set) int $watchWithInterval;
    protected(set) PasswordProviderList $passwordProvider;
    protected(set) MFAProvider $mfaProvider;

    public function __construct()
    {
        $this->loadDefaults();
    }

    protected function loadDefaults(): void
    {
        $this->allowDestructiveActions = App::config()->get('icloudpd.allowDestructiveActions', false);
        $this->name = App::config()->get('icloudpd.name', 'icloudpd');
        $this->installPath = App::config()->get('icloudpd.install_path');

        foreach (App::config()->get('icloudpd.defaults', []) as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function directory(string $directory): static
    {
        $this->directory = $directory;
        return $this;
    }

    public function authOnly(bool $value = true): static
    {
        $this->authOnly = $value;
        return $this;
    }

    public function cookieDirectory(string $directory): static
    {
        $this->cookieDirectory = $directory;
        return $this;
    }

    public function size(Size|SizeList $size, bool $overrideExisting = false): static
    {
        if ($overrideExisting) {
            $this->clear('size');
        }

        if (!isset($this->size)) {
            $this->size = new SizeList();
        }

        $this->size->add($size);

        return $this;
    }

    public function clear(string $property): static
    {
        if (array_key_exists($property, static::PARAMETERS)) {
            unset($this->$property);
        }
        return $this;
    }

    public function livePhotoSize(LivePhotoSize $size): static
    {
        $this->livePhotoSize = $size;
        return $this;
    }

    public function recent(int $number): static
    {
        $this->recent = $number;
        return $this;
    }

    public function untilFound(int $number): static
    {
        $this->untilFound = $number;
        return $this;
    }

    public function album(array|string $albums): static
    {
        $this->album = is_array($albums) ? $albums : [$albums];
        return $this;
    }

    public function listAlbums(bool $value = true): static
    {
        $this->listAlbums = $value;
        return $this;
    }

    public function library(string $library): static
    {
        $this->library = $library;
        return $this;
    }

    public function listLibraries(bool $value = true): static
    {
        $this->listLibraries = $value;
        return $this;
    }

    public function skipVideos(bool $value = true): static
    {
        $this->skipVideos = $value;
        return $this;
    }

    public function skipLivePhotos(bool $value = true): static
    {
        $this->skipLivePhotos = $value;
        return $this;
    }

    public function xmpSidecar(bool $value = true): static
    {
        $this->xmpSidecar = $value;
        return $this;
    }

    public function forceSize(bool $value = true): static
    {
        $this->forceSize = $value;
        return $this;
    }

    public function autoDelete(bool $value = true): static
    {
        $this->autoDelete = $value;
        return $this;
    }

    public function folderStructure(string $format): static
    {
        $this->folderStructure = $format;
        return $this;
    }

    public function setExifDatetime(bool $value = true): static
    {
        $this->setExifDatetime = $value;
        return $this;
    }

    public function smtpUsername(string $username): static
    {
        $this->smtpUsername = $username;
        return $this;
    }

    public function smtpPassword(string $password): static
    {
        $this->smtpPassword = $password;
        return $this;
    }

    public function smtpHost(string $host): static
    {
        $this->smtpHost = $host;
        return $this;
    }

    public function smtpPort(int $port): static
    {
        $this->smtpPort = $port;
        return $this;
    }

    public function smtpNoTLS(bool $value = true): static
    {
        $this->smtpNoTLS = $value;
        return $this;
    }

    public function notificationEmail(string $email): static
    {
        $this->notificationEmail = $email;
        return $this;
    }

    public function notificationEmailFrom(string $email): static
    {
        $this->notificationEmailFrom = $email;
        return $this;
    }

    public function notificationScript(string $path): static
    {
        $path = realpath($path);

        if (file_exists($path)) {
            $this->notificationScript = $path;
        }

        return $this;
    }

    public function keepRecentDays(int $days): static
    {
        $this->keepRecentDays = $days;
        return $this;
    }

    public function dryRun(bool $value = true): static
    {
        $this->dryRun = $value;
        return $this;
    }

    public function keepUnicodeInFilenames(bool $value = true): static
    {
        $this->keepUnicodeInFilenames = $value;
        return $this;
    }

    public function watchWithInterval(int $seconds): static
    {
        $this->watchWithInterval = $seconds;
        return $this;
    }

    public function livePhotoFilenamePolicy(LivePhotoFilenamePolicy $policy): static
    {
        $this->livePhotoFilenamePolicy = $policy;
        return $this;
    }

    public function alignRaw(AlignRaw $option): static
    {
        $this->alignRaw = $option;
        return $this;
    }

    public function fileMatchPolicy(FileMatchPolicy $policy): static
    {
        $this->fileMatchPolicy = $policy;
        return $this;
    }

    public function skipCreatedBefore(string $timestamp): static
    {
        $this->skipCreatedBefore = $timestamp;
        return $this;
    }

    public function skipCreatedAfter(string $timestamp): static
    {
        $this->skipCreatedAfter = $timestamp;
        return $this;
    }

    public function skipPhotos(bool $value = true): static
    {
        $this->skipPhotos = $value;
        return $this;
    }

    public function username(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function password(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function version(): static
    {
        $this->clearAll();
        $this->version = true;
        return $this;
    }

    public function clearAll(): static
    {
        foreach (array_keys(static::PARAMETERS) as $parameter) {
            unset($this->$parameter);
        }
        return $this;
    }

    public function useOSLocale(bool $value = true): static
    {
        $this->useOSLocale = $value;
        return $this;
    }

    public function onlyPrintFilenames(bool $value = true): static
    {
        $this->onlyPrintFilenames = $value;
        return $this;
    }

    public function logLevel(LogLevel $level): static
    {
        $this->logLevel = $level;
        return $this;
    }

    public function noProgressBar(bool $value = true): static
    {
        $this->noProgressBar = $value;
        return $this;
    }

    public function domain(Domain $domain): static
    {
        $this->domain = $domain;
        return $this;
    }

    public function passwordProvider(
        PasswordProvider|PasswordProviderList $providers,
        bool $overrideExisting = false,
    ): static {
        if ($overrideExisting) {
            $this->clear('passwordProvider');
        }

        if (!isset($this->passwordProvider)) {
            $this->passwordProvider = new PasswordProviderList();
        }

        $this->passwordProvider->add($providers);

        return $this;
    }

    public function mfaProvider(MFAProvider $provider): static
    {
        $this->mfaProvider = $provider;
        return $this;
    }

    public function reset(): static
    {
        $this->clearAll()->loadDefaults();
        return $this;
    }

    public function __toString(): string
    {
        return $this->build();
    }

    public function build(): string
    {
        $command = $this->name ?? 'icloudpd';

        if (!empty($this->installPath)) {
            $path = realpath($this->installPath);

            if ($path) {
                if (!str_ends_with($path, DIRECTORY_SEPARATOR)) {
                    $path .= DIRECTORY_SEPARATOR;
                }
                $command = $path . $command;
            }
        }

        return $command . ' ' . implode(' ', $this->parameters());
    }

    public function parameters(): array
    {
        $stack = [];

        foreach (array_keys(static::PARAMETERS) as $property) {
            if (isset($this->$property)) {
                $parameter = $this->buildParameter($property);
                if ($parameter !== null) {
                    $stack[] = $parameter;
                }
            }
        }

        return $stack;
    }

    protected function buildParameter(string $property): ?string
    {
        if (in_array($property, static::DESTRUCTIVE_ACTIONS) && !$this->allowDestructiveActions) {
            return null;
        }

        return ParameterBuilder::from(static::PARAMETERS[$property], $this->$property);
    }
}