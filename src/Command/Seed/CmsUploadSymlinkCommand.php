<?php

namespace App\Command\Seed;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:seed:cms-upload-symlink',
    description: 'CMSアップロードディレクトリのシンボリックリンク作成',
)]
class CmsUploadSymlinkCommand extends Command
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

        $upDir = $this->parameterBag->get('cms_upload_dir');
        $htaccess = $upDir. "/.htaccess";
        $symLink = $this->parameterBag->get('cms_upload_dir_symlink');

        $fs = new Filesystem();
        if(!$fs->exists($upDir)) {
            $fs->mkdir($upDir, 0775);
            $io->note('Upload dir create. '. $upDir);
        }
        if(!$fs->exists($htaccess)) {
            $htaccessSource = $this->parameterBag->get('kernel.project_dir')."/resources/seed/.htaccess";
            $fs->copy($htaccessSource, $htaccess);
            $io->note('upload dir .htaccess file assign.');
        }
        if(!$fs->exists($symLink)) {
            $fs->symlink($upDir, $symLink, true);
            $io->note('Upload dir symlink create. '. $symLink);
        }

        return Command::SUCCESS;
    }
}
