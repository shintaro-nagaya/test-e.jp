<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init',
    description: '初期設定',
)]
class InitCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $command = $this->getApplication()->find('app:init:htaccess');
        $command->run($input, $output);

        $command = $this->getApplication()->find('app:init:docker-compose');
        $command->run($input, $output);

        $command = $this->getApplication()->find('app:init:env');
        $command->run($input, $output);

        return Command::SUCCESS;
    }
}
