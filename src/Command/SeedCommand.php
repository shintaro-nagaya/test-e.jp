<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed',
    description: 'DBのシードを行う',
)]
class SeedCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('refresh', null, InputOption::VALUE_NONE, 'シード実行前にDBを削除・再構築する')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        if($input->getOption('refresh')) {
            $q = new ConfirmationQuestion(
                "DBを再構築しますか? [y]: ", false, "/^y/i"
            );
            if($helper->ask($input, $output, $q)) {
                $io->text('Drop Database');
                $com = $this->getApplication()->find("doctrine:database:drop");
                $com->run(new ArrayInput(["--force" => true]), $output);

                $io->text('Create Database');
                $com = $this->getApplication()->find("doctrine:database:create");
                $com->run(new ArrayInput([]), $output);

                $io->text('Migration...');
                $com = $this->getApplication()->find("doctrine:migrations:migrate");
                $com->run(new ArrayInput(["--no-interaction" => true]), $output);
            }
        }
        foreach([
            "app:seed:cms-upload-symlink",
            "app:seed:account",
            "app:seed:news-category",
            "app:seed:news-entry",
            "assets:install"
        ] as $appName) {
            $com = $this->getApplication()->find($appName);
            $com->run(new ArrayInput([]), $output);
        }

        $io->success("Databaseシード完了");

        return Command::SUCCESS;
    }
}
