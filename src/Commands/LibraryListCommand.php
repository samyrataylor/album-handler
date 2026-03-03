<?php

namespace SamyraTaylor\AlbumHandler\Commands;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\combinedClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LibraryListCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('library:list');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $libraries = new combinedClass()->listLibraries();

        $io->writeln($libraries);

        return 0;
    }
}