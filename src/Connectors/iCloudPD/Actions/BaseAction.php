<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Actions;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Client;
use SamyraTaylor\AlbumHandler\Connectors\Shell\BackgroundProcess;
use SamyraTaylor\AlbumHandler\Connectors\Shell\Process;
use SamyraTaylor\AlbumHandler\Data\User;
use SamyraTaylor\AlbumHandler\Exceptions\ActionException;
use SamyraTaylor\AlbumHandler\Exceptions\MissingCredentialsException;
use SamyraTaylor\AlbumHandler\Support\UserProcesses;

abstract class BaseAction
{
    public static RunType $runType = RunType::Normal;
    public static bool $requiresAuth = false;
    protected(set) array $arguments = [];
    protected Process $process;
    protected User $user;
    protected(set) bool $isQueuedProcess = false;
    protected(set) int $queuedProcessId;

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

        $this->process = $this->createProcess();

        if(!$this->isQueuedProcess) {
            return $this->handle($this->beforeRun($this->process)->run());
        }

        if(UserProcesses::canStartProcess($this->user)) {
            $this->process = $this->beforeRun(
                UserProcesses::getQueuedProcess($this->user, $this->queuedProcessId)
            );

            $process = UserProcesses::startQueuedProcess(
                user: $this->user,
                queueId: $this->queuedProcessId,
                mutated: $this->process instanceof BackgroundProcess ? $this->process : null
            );

            if(!$process) {
                return null;
            }

            $this->process = $process;

            return $this->handle($this->process);
        }

        return null;
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
            $bgProcess = new BackgroundProcess($this->client->build())->pipeOutput($this->pipeProcessOutput());

            if(static::$requiresAuth && !empty($this->user)) {
                $this->isQueuedProcess = true;
                $this->queuedProcessId = UserProcesses::queueProcess($this->user, $bgProcess);
            }
            $this->process = $bgProcess;
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