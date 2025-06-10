<?php

namespace App\Command\Dev;

use App\Service\MailService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:smtp-test',
    description: 'SMTP送信テストを行う',
)]
class SmtpCommand extends Command
{
    public function __construct(
        private readonly MailService $mailService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('to', InputArgument::OPTIONAL, 'Send Email Address');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $to = $input->getArgument('to');

        if (!$to) {
            $to = "info@triple-e.inc";
        }
        $config =
            $this->mailService->send(
                $this->mailService->configureOptions()->resolve([
                    "to" => [$to],
                    "subject" => "SMTPサーバーテストです",
                    "message" => "SMTPサーバーの設定テストのためのメール送信です"
                ])
            );

        return Command::SUCCESS;
    }
}
