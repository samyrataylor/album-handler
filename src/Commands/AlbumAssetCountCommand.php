<?php

namespace SamyraTaylor\AlbumHandler\Commands;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\combinedClass;
use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\iCloudPD;
use SamyraTaylor\AlbumHandler\Data\User;
use SamyraTaylor\AlbumHandler\Exceptions\ActionException;
use SamyraTaylor\AlbumHandler\Support\App;
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
             ->addArgument('user', InputArgument::REQUIRED)
             ->addArgument('album', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getArgument('user') ?? App::env('DEFAULT_USER');
        $album = $input->getArgument('album');

        try {
            $assets = iCloudPD::user(User::load($name))->countAlbumAssets($album);
        } catch (ActionException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->success(sprintf('%d assets in album "%s"', $assets, $album));

        return Command::SUCCESS;
    }
}