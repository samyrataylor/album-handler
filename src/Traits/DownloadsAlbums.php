<?php

namespace SamyraTaylor\AlbumHandler\Traits;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\iCloudPD;
use SamyraTaylor\AlbumHandler\Data\User;
use SamyraTaylor\AlbumHandler\Exceptions\ActionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

trait DownloadsAlbums
{
    use Downloads;

    protected function downloadAlbum(User $user, string $album, SymfonyStyle $io, bool $progressBar = true): int
    {
        $io->writeln(sprintf('→ Downloading "%s"', $album));

        try {
            $process = iCloudPD::user($user)->downloadAlbum($album);
        } catch (ActionException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        if ($process === null) {
            $io->error(sprintf('Failed to download album "%s".', $album));

            return Command::FAILURE;
        }

        $io->writeln(sprintf('<comment>  ↳ Background PID: %d</comment>', $process->pid));

        return $this->download($process, $io, $progressBar);
    }
}