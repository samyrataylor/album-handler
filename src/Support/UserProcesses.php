<?php

namespace SamyraTaylor\AlbumHandler\Support;

use SamyraTaylor\AlbumHandler\Connectors\Shell\BackgroundProcess;
use SamyraTaylor\AlbumHandler\Data\User;

class UserProcesses
{
    protected(set) static array $running = [];
    protected(set) static array $stopped = [];

    public static function addUserProcess(User $user, BackgroundProcess $process, bool $updateProcesses = true): bool
    {
        if ($updateProcesses) {
            self::updateProcesses();
        }

        $processes = self::getUserProcesses($user, false)['running'];

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

    public static function updateProcesses(): void
    {
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

    public static function getUserProcesses(User $user, bool $updateProcesses = true): array
    {
        if ($updateProcesses) {
            self::updateProcesses();
        }

        return [
            'running' => self::$running[$user->alias] ?? [],
            'stopped' => self::$stopped[$user->alias] ?? [],
        ];
    }
}