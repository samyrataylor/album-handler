<?php

namespace SamyraTaylor\AlbumHandler\Commands;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\iCloudPD;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class VersionCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('version');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln(new iCloudPD()->version());

        return 0;
    }
}