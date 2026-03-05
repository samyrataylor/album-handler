<?php

namespace SamyraTaylor\AlbumHandler\Commands;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\iCloudPD;
use SamyraTaylor\AlbumHandler\Data\User;
use SamyraTaylor\AlbumHandler\Exceptions\ActionException;
use SamyraTaylor\AlbumHandler\Support\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AlbumListCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('album:list')
             ->addArgument('user', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('user') ?? App::env('DEFAULT_USER');

        try {
            $assets = iCloudPD::user(User::load($name))->listAlbums();
        } catch (ActionException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->table(['Album Name'], array_map(fn(string $name) => [$name], $assets));
        $io->success('Found ' . count($assets) . ' albums!');

//        $action = iCloudPD::listAlbums();
//
//
//
//
//
//
//        $pd = new combinedClass();
//
//        $io->write('→ Getting album listing from iCloud...');
//
//        $albums = $pd->listAlbums();
//
//        $io->writeln('Done!');
//
//
//        $io->writeln($albums);

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


        return Command::SUCCESS;
    }

}