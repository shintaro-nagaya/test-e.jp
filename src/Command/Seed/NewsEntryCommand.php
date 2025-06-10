<?php

namespace App\Command\Seed;

use App\Command\Traits\FileSeedTrait;
use App\Entity\News\Child;
use App\Entity\News\Entry;
use App\Repository\News\CategoryRepository;
use App\Repository\News\ChildRepository;
use App\Repository\News\EntryRepository;
use App\Utils\GetCsvTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:seed:news-entry',
    description: 'News記事をシーディング',
)]
class NewsEntryCommand extends Command
{
    use GetCsvTrait;
    use FileSeedTrait;

    public function __construct(
        private ParameterBagInterface $parameterBag,
        private EntryRepository $repository,
        private ChildRepository $childRepository,
        private CategoryRepository $categoryRepository
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $csvPath = $this->parameterBag->get('kernel.project_dir'). "/resources/seed/news/entry.csv";
        $spl = $this->getCsv($csvPath);
        foreach($spl as $k => $r) {
            if(!$k) continue;
            $entry = $this->repository->findOneBy(["id" => $r[0]]);
            if(!$entry) {
                $category = $this->categoryRepository->findOneBy(["id" => $r[1]]);
                if(!$category) {
                    $io->caution("Category ". $r[1]. " not found");
                    continue;
                }
                $entry = (new Entry())
                    ->setCategory($category)
                    ->setTitle($r[2])
                    ->setEntryDate(new \DateTime($r[3]))
                    ->setEnable(true)
                    ->setMainImage($r[4])
                    ->setMainImageWidth($r[5])
                    ->setMainImageHeight($r[6])
                    ->setThumbnail($r[7])
                    ->setContent($r[8])
                    ;
                $this->repository->add($entry);
            }
        }

        $csvPath = $this->parameterBag->get('kernel.project_dir'). "/resources/seed/news/child.csv";
        $spl = $this->getCsv($csvPath);
        foreach($spl as $k => $r) {
            if (!$k) continue;
            $child = $this->childRepository->findOneBy(["id" => $r[0]]);
            if(!$child) {
                $entry = $this->repository->findOneBy(["id" => $r[1]]);
                if(!$entry) {
                    $io->caution("entry ". $r[1]. " not found");
                    continue;
                }
                $child = (new Child())
                    ->setEntry($entry)
                    ->setSort($r[2])
                    ->setHeadline($r[3])
                    ->setImage($r[4])
                    ->setImageWidth($r[5])
                    ->setImageHeight($r[6])
                    ->setContent($r[7])
                    ->setYoutubeId($r[8])
                    ;
                $this->childRepository->add($child);
            }
        }
        $this->copyFiles(
            Entry::class,
            "mainImage",
            $this->parameterBag->get('kernel.project_dir'). "/resources/seed/news/images/"
        );

        $io->success('News記事　完了');

        return Command::SUCCESS;
    }
}
