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
    name: 'app:init:docker-compose',
    description: 'docker-compose.ymlファイルを作成',
)]
class DockerComposeYamlCommand extends Command
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

        $destPath = $this->parameterBag->get('kernel.project_dir'). "/docker-compose.yml";
        if(!$fs->exists($destPath)) {
            $src = $this->parameterBag->get('resource_dir'). "/init/docker-compose.yml";
            $fs->copy($src, $destPath);
            $io->text('docker-compose.yml ファイル作成');
        }

        return Command::SUCCESS;
    }
}
