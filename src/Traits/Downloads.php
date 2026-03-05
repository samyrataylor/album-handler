<?php

namespace SamyraTaylor\AlbumHandler\Traits;

use SamyraTaylor\AlbumHandler\Connectors\Shell\BackgroundProcess;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

trait Downloads
{
    protected function download(BackgroundProcess $process, SymfonyStyle $io, bool $progressBar = true): int
    {
        do {
            usleep(10000);

            $total = array_values(
                array_filter(
                    $process->output(),
                    fn(string $item) => str_contains($item, 'original')
                )
            );
        } while ($total === []);

        $total = (int)preg_replace('/^.* Downloading ([0-9]+) .*/', '\1', $total[0]);

        if($progressBar) {
            $io->newLine();
            $progress = $io->createProgressBar($total);
            $progress->start();
        }

        while ($process->running()) {
            $process->updateState();

            if($progressBar) {
                $mapped = array_map(
                    callback: fn(string $line) => preg_replace(
                        pattern: '/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]*:[0-9]*:[0-9]* (INFO|DEBUG)\s*(.*)$/',
                        replacement: '\2',
                        subject: $line),
                    array: $process->output()
                );

                $filtered = array_filter(
                    array: $mapped,
                    callback: function (string $line) {
                        if (str_ends_with($line, 'already exists')) {
                            return true;
                        }

                        if(!str_starts_with($line, 'Downloaded ')) {
                            return false;
                        }

                        return !str_contains($line, '-original')
                            && !str_contains($line, '-adjusted')
                            && !str_contains($line, '-alternative')
                            && !str_contains($line, '_HEVC.MOV');
                    }
                );

                if(count($filtered) > $total) {
                    $temp = array_filter(
                        array: $filtered,
                        callback: function (string $line) use ($filtered){
                            if(!str_ends_with($line, 'MOV')) {
                                return true;
                            }

                            $instances = array_filter(
                                array: $filtered,
                                callback: fn(string $item) => str_contains($item, substr($line, -3))
                            );

                            return count($instances) === 1;
                        }
                    );

                    $filtered = $temp;
                }

                $progress->setProgress(count($filtered));
            }

            usleep(250000);
        }

        if ($process->executed()) {
            if($progressBar) {
                $progress->finish();
                $io->newLine(2);
            } else {
                $io->writeln(sprintf('<info>  ↳ Downloaded %d assets</info>', $total));
            }
        }

        return Command::SUCCESS;
    }
}