<?php

namespace SamyraTaylor\AlbumHandler\Commands;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\iCloudPD;
use SamyraTaylor\AlbumHandler\Data\User;
use SamyraTaylor\AlbumHandler\Exceptions\ActionException;
use SamyraTaylor\AlbumHandler\Traits\DownloadsAlbums;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AlbumDownloadCommand extends Command
{
    use DownloadsAlbums;

    protected function configure(): void
    {
        $this->setName('album:download')
             ->addArgument('user', InputArgument::REQUIRED)
             ->addArgument('album', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = User::load($input->getArgument('user'));
        $album = $input->getArgument('album');

        try {
            $albums = iCloudPD::user($user)->listAlbums();
        } catch (ActionException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        if (!in_array($album, $albums)) {
            $io->error(sprintf('Album %s does not exist.', $album));
            return Command::FAILURE;
        }

        $code = $this->downloadAlbum($user, $album, $io);

        if($code === Command::SUCCESS) {
            $io->newLine(2);
            $io->success(sprintf('Album %s downloaded successfully!', $album));
        } else {
            $io->error('Download failed');
        }

        return $code;
    }
}