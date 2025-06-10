<?php

namespace App\Command\Seed;

use App\Entity\News\Category;
use App\Repository\News\CategoryRepository;
use App\Utils\GetCsvTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'app:seed:news-category',
    description: 'Newsカテゴリーをシーディング',
)]
class NewsCategoryCommand extends Command
{
    use GetCsvTrait;

    public function __construct(
        private ParameterBagInterface $parameterBag,
        private CategoryRepository $repository
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $csvPath = $this->parameterBag->get('kernel.project_dir'). "/resources/seed/news/category.csv";
        $spl = $this->getCsv($csvPath);
        foreach ($spl as $k => $r) {
            if(!$k) continue;
            $category = $this->repository->findOneBy(["id" => $r[0]]);
            if(!$category) {
                $category = (new Category())
                    ->setName($r[1])
                    ->setSort($r[2])
                    ->setEnable(true)
                    ;
                $this->repository->add($category);
            }
        }
        $io->success("Newsカテゴリー 完了");

        return Command::SUCCESS;
    }
}
