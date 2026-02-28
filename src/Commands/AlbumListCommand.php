<?php

namespace SamyraTaylor\AlbumHandler\Commands;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\iCloudPD;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AlbumListCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('album:list');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $pd = new iCloudPD();

        $io->write('→ Getting album listing from iCloud...');

        $albums = $pd->listAlbums();

        $io->writeln('Done!');


        $io->writeln($albums);

//        $pd = new iCloudPD();
//        dd($pd->version());
//
//
//        $io->writeln('→ Getting album listing from iCloud...');
//        $albums = $pd->listAlbums();
//
//        $io->writeln('<info>  ↳ Found '.count($albums).' albums</info>');
//
//        $io->writeln('→ Getting asset counts for all albums...');
//
//        foreach($albums as $album) {
//            $io->write('  ↳ ' . $album. ': ');
//            $io->writeln('<info>'.$pd->getAssetCount($album).'</info>');
//        }

        return 0;
    }

}