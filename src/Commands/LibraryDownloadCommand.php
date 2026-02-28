<?php

namespace SamyraTaylor\AlbumHandler\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LibraryDownloadCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('library:download')
             ->addArgument('name', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}