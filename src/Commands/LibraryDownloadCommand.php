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

class LibraryDownloadCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('library:download')
             ->addArgument('user', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $user = User::load($input->getArgument('user'));

        try {
            $bg = iCloudPD::user($user)->downloadLibrary();
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

        $io->success('Started downloading library from iCloud.');

        return Command::SUCCESS;
    }
}