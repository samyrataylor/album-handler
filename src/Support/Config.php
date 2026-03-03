<?php

namespace SamyraTaylor\AlbumHandler\Support;

use Adbar\Dot;
use Symfony\Component\Finder\Finder;

class Config
{
    protected(set) Finder $finder;
    protected(set) array $files;

    protected(set) array $configMap = [];

    protected(set) Dot $config;

    public function __construct(
        protected(set) string $basePath,
    ) {
        $path = [
            'base'   => $basePath,
            'config' => $basePath . '/config',
            'app'    => $basePath . '/src',
            'user'   => $basePath . '/user',
        ];

        $this->configMap['path'] = $path;

        $this->finder = new Finder();
        $this->finder->in($path['config'])->name('*.php');

        foreach ($this->finder as $file) {
            $this->files[] = $file;
            $this->configMap[$file->getFilenameWithoutExtension()] = include $file->getRealPath();
        }

        $this->config = new Dot($this->configMap);
    }

    public function all(): array
    {
        return $this->config->all();
    }

    public function make(): static
    {
        return App::config();
    }

    public function userPath(?string $path = null): string
    {
        return $this->path($path, $this->get('path.user'));
    }

    public function path(?string $path = null, ?string $base = null): string
    {
        $base ??= $this->get('path.base');
        $path = is_null($path) ? $base : Str::finish($base, DIRECTORY_SEPARATOR) . $path;

        return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config->get($key, $default);
    }

    public function basePath(?string $path = null): string
    {
        return $this->path($path, $this->get('path.base'));
    }

    public function configPath(?string $path = null): string
    {
        return $this->path($path, $this->get('path.config'));
    }

    public function appPath(?string $path = null): string
    {
        return $this->path($path, $this->get('path.app'));
    }
}