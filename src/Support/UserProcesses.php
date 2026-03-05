<?php

namespace SamyraTaylor\AlbumHandler\Support;

use SamyraTaylor\AlbumHandler\Connectors\Shell\BackgroundProcess;
use SamyraTaylor\AlbumHandler\Data\User;

class UserProcesses
{
    protected(set) static array $queued = [];
    protected(set) static array $running = [];
    protected(set) static array $stopped = [];

    public static function queueProcess(User $user, BackgroundProcess $process, bool $updateProcesses = true): int
    {
        self::updateProcesses($updateProcesses);

        foreach(self::$queued[$user->alias] ?? [] as $qid => $queued) {
            if($queued == $process) {
                return $qid;
            }
        }

        $id = rand();
        self::$queued[$user->alias][$id] = $process;

        return $id;
    }

    public static function updateProcesses(bool $update = true): void
    {
        if (!$update) {
            return;
        }

        foreach (self::$running as $alias => $processes) {
            /** @var BackgroundProcess $process */
            foreach ($processes as $pid => $process) {
                $process->updateState();
                if ($process->executed()) {
                    unset(self::$running[$alias][$pid]);
                    self::$stopped[$alias][$pid] = $process;
                }
            }
        }
    }

    public static function startQueuedProcess(
        User $user,
        int $queueId,
        ?BackgroundProcess $mutated = null,
        bool $updateProcesses = true,
    ): ?BackgroundProcess {
        self::updateProcesses($updateProcesses);

        $queued = self::getQueuedProcess($user, $queueId, false);

        if (!$queued) {
            return null;
        }

        if (self::canStartProcess($user)) {
            $process = $mutated ?? $queued;
            $process->run();
            unset(self::$queued[$user->alias][$queueId]);
            self::addProcess($user, $process, false);
            usleep(10000);
            self::updateProcesses();
            return $process;
        }

        return null;
    }

    public static function canStartProcess(User $user): bool
    {
        return count(self::getProcesses($user)['running']) < App::config()->get('app.maxUserProcesses', 1);
    }

    public static function getQueuedProcess(User $user, int $queueId, bool $updateProcesses = true): ?BackgroundProcess
    {
        self::updateProcesses($updateProcesses);
        return self::$queued[$user->alias][$queueId] ?? null;
    }

    public static function getProcesses(User $user, bool $updateProcesses = true): array
    {
        self::updateProcesses($updateProcesses);

        return [
            'queued'  => self::$queued[$user->alias] ?? [],
            'running' => self::$running[$user->alias] ?? [],
            'stopped' => self::$stopped[$user->alias] ?? [],
        ];
    }

    public static function addProcess(User $user, BackgroundProcess $process, bool $updateProcesses = true): bool
    {
        self::updateProcesses($updateProcesses);

        $processes = self::getProcesses($user, false)['running'];

        if (in_array($process, $processes)) {
            return false;
        }

        $process->updateState();

        if ($process->running()) {
            self::$running[$user->alias][$process->pid] = $process;
        } else {
            self::$stopped[$user->alias][$process->pid] = $process;
        }

        return true;
    }

    public static function getAllProcesses(bool $updateProcesses = true): array
    {
        self::updateProcesses($updateProcesses);

        return [
            'queued'  => self::$queued ?? [],
            'running' => self::$running ?? [],
            'stopped' => self::$stopped ?? [],
        ];
    }
}