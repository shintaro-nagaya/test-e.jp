<?php

namespace App\Command\Generate;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TripleE\SourceGenerator\AbstractCommandInput;
use TripleE\SourceGenerator\Inquiry\CommandInput;

#[AsCommand(
    name: 'app:generate:inquiry',
    description: '問い合わせフォーム生成',
)]
class InquiryCommand extends Command
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $command = new CommandInput(
            $input,
            $output,
            $this->getHelper('question'),
            $this->parameterBag->get('kernel.project_dir')
        );
        if($command->exec() === AbstractCommandInput::DONE) {
            dump($command->getGeneratedFiles());

            $io->note('生成完了 以下手動で実行してください');
            $io->text('管理画面のメニューに追加');
            $io->text('マイグレーション');
            $io->text('php bin/console make:migration');
            $io->text('php bin/console doctrine:migrations:migrate');
            $io->note('サイトマップ追加');
            $io->text('config/services.yaml sitemap_fixed: に');
            $io->text($command->generator->getMvcControllerGenerator()->createRouteName('index'). ' を追加');
            return Command::SUCCESS;
        } else {
            $io->note('cancel');
            return Command::FAILURE;
        }
    }
}
