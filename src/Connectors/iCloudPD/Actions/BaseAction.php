<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Connectors\Shell\BackgroundProcess;
use SamyraTaylor\AlbumHandler\Connectors\Shell\Process;
use SamyraTaylor\AlbumHandler\Data\User;
use SamyraTaylor\AlbumHandler\Exceptions\ActionException;
use SamyraTaylor\AlbumHandler\Exceptions\MissingCredentialsException;

abstract class BaseAction
{
    public static RunType $runType = RunType::Normal;
    public static bool $requiresAuth = false;
    protected(set) array $arguments = [];
    protected Process $process;
    protected User $user;

    public function __construct(
        protected Client $client,
    ) {}

    /**
     * @throws ActionException
     */
    public function run(...$args): mixed
    {
        if (static::$requiresAuth && empty($this->user)) {
            throw new MissingCredentialsException('No user set.');
        }

        $this->arguments = $this->parseArguments($args);
        $this->client = $this->build($this->client);

        return $this->handle($this->beforeRun($this->createProcess())->run());
    }

    protected function parseArguments(array $arguments): array
    {
        return $arguments;
    }

    abstract protected function build(Client $client): Client;

    abstract protected function handle(Process $process): mixed;

    protected function beforeRun(Process $process): Process
    {
        return $process;
    }

    protected function createProcess(): Process
    {
        if (static::$runType === RunType::Background) {
            $this->process = new BackgroundProcess($this->client->build())->pipeOutput($this->pipeProcessOutput());
        } else {
            $this->process = new Process($this->client->build());
        }

        return $this->process;
    }

    protected function pipeProcessOutput(): string
    {
        return '/dev/null';
    }

    public function user(User $user): static
    {
        if (static::$requiresAuth) {
            $this->user = $user;
        }

        return $this;
    }

    protected function getArgument(string|int $name, mixed $default = null): mixed
    {
        return $this->arguments[$name] ?? $default;
    }
}