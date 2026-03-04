<?php

namespace SamyraTaylor\AlbumHandler\Commands;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\iCloudPD;
use SamyraTaylor\AlbumHandler\Data\User;
use SamyraTaylor\AlbumHandler\Exceptions\ActionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AlbumDownloadCommand extends Command
{
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
            $bg = iCloudPD::user($user)->downloadAlbum($album);
        } catch (ActionException $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

        $io->info([
            'To follow output:',
            '$ tail -f ' . $bg->pipeOutput,
        ]);

        $io->info([
            'To kill process:',
            '$ kill ' . $bg->pid,
        ]);

        $io->success(sprintf('Started downloading album "%s" from iCloud.', $album));

        return Command::SUCCESS;
    }
}