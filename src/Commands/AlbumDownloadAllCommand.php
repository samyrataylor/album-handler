<?php

namespace SamyraTaylor\AlbumHandler\Commands;

use SamyraTaylor\AlbumHandler\Connectors\iCloudPD\iCloudPD;
use SamyraTaylor\AlbumHandler\Data\User;
use SamyraTaylor\AlbumHandler\Exceptions\ActionException;
use SamyraTaylor\AlbumHandler\Support\App;
use SamyraTaylor\AlbumHandler\Traits\DownloadsAlbums;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AlbumDownloadAllCommand extends Command
{
    use DownloadsAlbums;

    protected function configure(): void
    {
        $this->setName('album:download-all')
             ->addOption('user', 'u', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
             ->addOption('no-progress', 'b', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $defaultUser = App::env('DEFAULT_USER');
        $users = $input->getOption('user') ?? [];

        if (empty($users)) {
            if (empty($defaultUser)) {
                $io->error('At least one user is required.');

                return Command::FAILURE;
            }

            $users = [$defaultUser];
        }

        foreach ($users as $alias) {
            $io->writeln('→ Loading user ' . $alias . '...');
            $user = User::load($alias);

            if (!$user->exists) {
                $io->error('User does not exist.');

                return Command::FAILURE;
            }

            $io->writeln('<info>  ↳ Found user info for ' . $user->name . '!</info>');
            $io->writeln('→ Testing authentication');

            try {
                $auth = iCloudPD::user($user)->authOnly();
            } catch (ActionException $e) {
                $io->newLine();
                $io->error($e->getMessage());

                return Command::FAILURE;
            }

            if (!$auth) {
                $io->writeln('<error>  ↳ FAILED</error>');
                $io->writeln('<error>  ↳ Skipping user</error>');
                continue;
            }

            $io->writeln('<info>  ↳ SUCCESS</info>');

            $io->writeln('→ Getting album listing from iCloud');

            try {
                $albums = iCloudPD::user($user)->listAlbums();
            } catch (ActionException $e) {
                $io->error($e->getMessage());

                return Command::FAILURE;
            }

            $io->writeln('<info>  ↳ Found ' . count($albums) . ' albums</info>');

            foreach ($albums as $album) {
                if(!$user->shouldDownloadAlbum($album)) {
                    $io->writeln(sprintf('→ Skipped "%s"', $album));
                    continue;
                }

                $download = $this->downloadAlbum($user, $album, $io, !$input->getOption('no-progress'));

                if($download !== Command::SUCCESS) {
                    $io->newLine(2);
                    $io->error('An error occurred');
                    return Command::FAILURE;
                }
            }
        }

        $io->newLine(2);
        $io->success('All albums have been downloaded.');

        return Command::SUCCESS;
    }
}