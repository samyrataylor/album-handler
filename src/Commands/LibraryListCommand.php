<?php

namespace SamyraTaylor\AlbumHandler\Commands;

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

        $io->warning('Currently only set up to sync the primary library.');

        return Command::SUCCESS;
    }
}