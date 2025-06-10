<?php

namespace App\Service\Entity\News;

use App\Entity\News\Child;
use App\Entity\News\Entry;
use App\Event\News\Entry\PostDeleteEvent;
use App\Event\News\Entry\PostPersistEvent;
use App\Event\News\Entry\PreDeleteEvent;
use App\Event\News\Entry\PrePersistEvent;
use App\Exception\CannotExecuteException;
use App\Form\News\ListType;
use App\Repository\News\CategoryRepository;
use App\Repository\News\ChildRepository;
use App\Repository\News\EntryRepository;
use App\Service\Entity\AbstractService;
use App\Service\Entity\Traits\HandleIndexTrait;
use App\Service\Entity\Traits\LoggingTrait;
use App\Service\Sitemap\BuilderService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use TripleE\Utilities\PaginationUtil;

class EntryService extends AbstractService
{
    use LoggingTrait;
    use HandleIndexTrait;

    public function __construct(
        private readonly EntryRepository $repository,
        private readonly CategoryRepository $categoryRepository,
        private readonly ChildRepository $childRepository,
        private readonly LoggerInterface $logger,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @return Entry
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createNewEntity(): Entry
    {
        return (new Entry())
            ->setEnable(true)
            ->setEntryDate(new \DateTime())
            ->setCategory($this->categoryRepository->getHeadItem())
            ->addChild((new Child())->setSort(1))
            ;
    }

    public function persistEntity(Entry $entry, FormInterface $form): int
    {
        $isCreate = !$entry->getId();
        $children = $entry->getChildren();
        foreach($form->get('children') as $k => $childForm) {
            if(
                $childForm->get('delete')->getData() ||
                !$children[$k]->haveContent()
            ) {
                $this->childRepository->remove($children[$k], false);
                $entry->removeChild($children[$k]);
            }
        }
        $preEvent = new PrePersistEvent($entry, $isCreate);
        $this->eventDispatcher->dispatch($preEvent, "news_entry.pre_persist");

        $this->repository->add($entry);

        $postEvent = new PostPersistEvent($entry, $isCreate);
        $this->eventDispatcher->dispatch($postEvent, "news_entry.post_persist");

        $this->persistLog(
            "news entry",
            $isCreate,
            $entry->getId(),
            $entry->getTitle()
        );
        return $isCreate ? self::CREATED : self::UPDATED;
    }

    public function deleteEntity(Entry $entry): int
    {
        $id = $entry->getId();
        if(!$this->canDelete($entry)) {
            throw new CannotExecuteException("この記事は削除できません");
        }
        $preEvent = new PreDeleteEvent($entry);
        $this->eventDispatcher->dispatch($preEvent, "news_entry.pre_delete");

        $this->repository->remove($entry);

        $postEvent = new PostDeleteEvent($entry);
        $this->eventDispatcher->dispatch($postEvent, "news_entry.post_delete");

        $this->deleteLog("News entry", $id, $entry->getTitle());
        return self::DELETED;
    }

    public function canDelete(Entry $entry): bool
    {
        return true;
    }

    /**
     * 公開Entryデータを取得
     * @param int $id
     * @return Entry|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getEntryById(int $id): ?Entry
    {
        $qb = $this->repository->getIndexQueryBuilder("e", false);
        $qb
            ->andWhere($qb->expr()->eq('e.id', ":id"))
            ->setParameter("id", $id);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * 前後のEntryを取得
     * @param Entry $entry
     * @return array
     */
    public function getBeforeAfter(Entry $entry): array
    {
        return $this->repository->getBeforeAfterEntry($entry);
    }

    /**
     * サイト一覧ページのデータ取得
     * @param Request $request
     * @return PaginationUtil
     */
    public function getPagination(
        Request $request,
        int     $defaultLimit = 16
    ): PaginationUtil
    {
        return $this->handleIndex($request, $this->repository, ListType::class, $defaultLimit);
    }

    /**
     * Newsページをサイトマップに登録
     * @param BuilderService $sitemap
     * @return void
     */
    public function addSitemap(BuilderService $sitemap): void
    {
        // 一覧
        $sitemap->addUrlByRouteName("news_index");
        // カテゴリー
        $categories = $this->categoryRepository->getCategoriesHaveEntry();
        foreach($categories as $category) {
            $sitemap->addUrlByRouteName("news_index", [
                "Category" => $category[0]->getId()
            ], 0.8);
        }
        // 記事一覧
        $entries = $this->repository->getIndexQuery([])->getQuery()->getResult();
        foreach($entries as $entry) {
            $sitemap->addUrlByRouteName("news_detail", [
                "id" => $entry->getId()
            ]);
        }
    }
}