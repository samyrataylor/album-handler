<?php

namespace SamyraTaylor\AlbumHandler\Commands;

use SamyraTaylor\AlbumHandler\Support\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCreateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('user:create')
             ->addArgument('alias', InputArgument::OPTIONAL)
             ->addOption('name', 'N', InputOption::VALUE_REQUIRED)
             ->addOption('username', 'u', InputOption::VALUE_REQUIRED)
             ->addOption('password', 'p', InputOption::VALUE_REQUIRED)
             ->addOption('album-dir', 'A', InputOption::VALUE_REQUIRED)
             ->addOption('library-dir', 'L', InputOption::VALUE_REQUIRED)
             ->addOption('cookie-dir', 'C', InputOption::VALUE_REQUIRED)
             ->addOption('ignore-album', 'I', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY)
             ->addOption('confirmed', 'y', InputOption::VALUE_NONE)
             ->addOption('overwrite', 'o', InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $alias = $input->getArgument('alias');
        $name = $input->getOption('name');
        $username = $input->getOption('username');
        $password = $input->getOption('password');
        $albumDir = $input->getOption('album-dir');
        $libraryDir = $input->getOption('library-dir');
        $cookieDir = $input->getOption('cookie-dir');
        $ignoreAlbum = $input->getOption('ignore-album');
        $confirmed = $input->getOption('confirmed');
        $overwrite = $input->getOption('overwrite');
        $noInteraction = $input->getOption('no-interaction');

        while (empty($alias)) {
            if ($noInteraction) {
                $io->error('Alias is required.');

                return Command::FAILURE;
            }

            $alias = $io->ask('User Alias');
        }

        while (empty($name)) {
            if ($noInteraction) {
                $io->error('Name is required.');

                return Command::FAILURE;
            }

            $name = $io->ask('Full Name');
        }

        while (empty($username)) {
            if ($noInteraction) {
                $io->error('Username is required.');

                return Command::FAILURE;
            }

            $username = $io->ask('Username/Email Address');
        }

        $password ??= $io->askHidden('Password');

        if ($confirmed || $io->confirm('Add directory mappings?')) {
            !empty($albumDir) ? $io->info('Albums Directory: ' . $albumDir) :
                ($albumDir = $io->ask('Albums Directory'));
            !empty($libraryDir) ? $io->info('Library Directory: ' . $libraryDir) :
                ($libraryDir = $io->ask('Library Directory'));
            !empty($cookieDir) ? $io->info('Cookie Directory: ' . $cookieDir) :
                ($cookieDir = $io->ask('Cookie Directory'));
        }

        $json = [
            'name' => $name,
            'auth' => [
                'username' => $username,
            ],
        ];

        if ($password) {
            $json['auth']['password'] = $password;
        }

        if ($albumDir) {
            $json['dir']['albums'] = $albumDir;
        }


        if ($libraryDir) {
            $json['dir']['library'] = $libraryDir;
        }


        if ($cookieDir) {
            $json['dir']['cookie'] = $cookieDir;
        }

        if ($ignoreAlbum) {
            $json['ignore_albums'] = $ignoreAlbum;
        }

        $json = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $io->writeln($json);

        if (!$confirmed && $noInteraction) {
            $io->error('Use --confirmed|-y to confirm generation when using --no-interaction.');

            return Command::FAILURE;
        }

        if ($confirmed || $io->confirm('Confirm generation?')) {
            if (!is_dir(App::config()->userPath($alias))) {
                mkdir(App::config()->userPath($alias));
            }

            $file = App::config()->userPath($alias . DIRECTORY_SEPARATOR . 'user.json');

            if (file_exists($file)) {
                $io->warning('User already exists.');

                if ($noInteraction) {
                    $io->error('User already exists, add --overwrite|-o to force generation.');

                    return Command::FAILURE;
                }

                if (!$overwrite && !$io->confirm('Overwrite user?', false)) {
                    $io->error('User already exists.');

                    return Command::FAILURE;
                }
            }

            file_put_contents($file, $json);

            $io->success($file . ' created successfully.');

            return Command::SUCCESS;
        }

        $io->info('User not created.');

        return Command::SUCCESS;
    }
}