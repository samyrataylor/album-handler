<?php

namespace SamyraTaylor\AlbumHandler\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AlbumDownloadCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('album:download');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}