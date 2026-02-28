<?php

namespace SamyraTaylor\AlbumHandler\Support;

use Dotenv\Dotenv;
use Symfony\Component\Console\Application;

class App
{
    private static self $instance;
    private(set) array $env;
    private(set) Config $config;
    private(set) Application $console;
    private bool $booted = false;

    protected function __construct(protected string $rootDir, bool $deferBoot = false)
    {
        if (!$deferBoot) {
            $this->boot();
        }
    }

    public function boot(): void
    {
        $this->bootEnv(Dotenv::createImmutable($this->rootDir)->safeLoad())
             ->bootConfig(new Config($this->rootDir))
             ->bootConsole(new Application());
    }

    public function bootConsole(Application $console): self
    {
        $this->checkState();

        if (!$this->booted) {
            $this->console = $console;
            $this->checkState();
        }

        return $this;
    }

    private function checkState(): void
    {
        $this->booted = isset($this->env) && isset($this->config) && isset($this->console);
    }

    public function bootConfig(Config $config): self
    {
        $this->checkState();

        if (!$this->booted) {
            $this->config = $config;
            $this->checkState();
        }

        return $this;
    }

    public function bootEnv(array $env): self
    {
        $this->checkState();

        if (!$this->booted) {
            $this->env = $env;
            $this->checkState();
        }

        return $this;
    }

    public static function config(): ?Config
    {
        return self::instance()?->config;
    }

    public static function instance(): ?self
    {
        if (!isset(self::$instance)) {
            return null;
        }

        $instance = self::$instance;

        return $instance->booted ? $instance : null;
    }

    public static function env(?string $key = null, mixed $default = null): mixed
    {
        if (!self::instance()) {
            return $default;
        }

        if ($key === null) {
            return self::instance()->env;
        }

        return self::instance()->env[$key] ?? $default;
    }

    public static function console(): ?Application
    {
        return self::instance()?->console;
    }

    public static function create(string $rootDir): self|false
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($rootDir);
        }
        return self::instance() ?? false;
    }
}