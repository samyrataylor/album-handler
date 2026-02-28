<?php

namespace SamyraTaylor\AlbumHandler\Commands;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\iCloudPD;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AlbumAssetCountCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('album:asset-count')
             ->addArgument('name', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pd = new iCloudPD();

        $io->writeln($pd->getAssetCount($input->getArgument('name')));

        return 0;
    }
}