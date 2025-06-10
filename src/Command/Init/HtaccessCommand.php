<?php

namespace App\Command\Init;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:init:htaccess',
    description: '.htaccessファイルを作成',
)]
class HtaccessCommand extends Command
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fs = new Filesystem();

        $destPath = $this->parameterBag->get('document_root'). "/.htaccess";
        if(!$fs->exists($destPath)) {
            $src = $this->parameterBag->get('resource_dir'). "/init/.htaccess";
            $fs->copy($src, $destPath);
            $io->text('.htaccessファイル作成');
        }

        return Command::SUCCESS;
    }
}
