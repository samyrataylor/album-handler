<?php

namespace SamyraTaylor\AlbumHandler\Traits;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\iCloudPD;
use SamyraTaylor\AlbumHandler\Data\User;
use SamyraTaylor\AlbumHandler\Exceptions\ActionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

trait DownloadsLibrary
{
    use Downloads;

    protected function downloadLibrary(User $user, SymfonyStyle $io, bool $progressBar = true): int
    {
        $io->writeln('→ Downloading Library...');

        try {
            $process = iCloudPD::user($user)->downloadLibrary();
        } catch (ActionException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        if ($process === null) {
            $io->error('Failed to download library.');

            return Command::FAILURE;
        }

        $io->info('PID: ' . $process->pid);

        return $this->download($process, $io, $progressBar);
    }
}