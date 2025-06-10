<?php

namespace App\Command\Dev\Notifier;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackImageBlockElement;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

#[AsCommand(
    name: 'dev:notifier:slack-test',
    description: 'slack通知をテストする',
)]
class SlackTestCommand extends Command
{
    public function __construct(
        private ChatterInterface $chatter
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $slackOptions = (new SlackOptions())
            ->block((new SlackSectionBlock())
                ->text('Slack option!')
                ->accessory(
                    new SlackImageBlockElement(
                        'https://symfony.com/favicons/apple-touch-icon.png',
                        'Symfony'
                    )
                )
            )
            ;
        $chatMessage = new ChatMessage('Test message');
//        $chatMessage->options($slackOptions);
        try {
            $res = $this->chatter->send($chatMessage);
            dump($res);
        } catch (\Exception $e) {
            dump($e);
        }

//
//        $arg1 = $input->getArgument('arg1');
//
//        if ($arg1) {
//            $io->note(sprintf('You passed an argument: %s', $arg1));
//        }
//
//        if ($input->getOption('option1')) {
//            // ...
//        }
//
//        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}