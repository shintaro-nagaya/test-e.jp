<?php

namespace App\Command;

use App\Service\Sitemap\BuilderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:dump-sitemap',
    description: 'sitemap.xmlを生成する',
)]
class DumpSitemapCommand extends Command
{
    public function __construct(private BuilderService $sitemap)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::OPTIONAL, 'dump filename (default: sitemap.xml)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('filename');

        if ($arg1) {
            $filename = $arg1;
        } else {
            $filename = "sitemap.xml";
        }

        $this->sitemap->registrationUrls();
        $path = $this->sitemap->dumpSitemap(
            $this->sitemap->createXml(),
            $filename
        );

        $io->success('sitemap file dump success. '. $path);

        return Command::SUCCESS;
    }
}
