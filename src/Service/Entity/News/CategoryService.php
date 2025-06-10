<?php

namespace App\Service\Entity\News;

use App\Entity\News\Category;
use App\Event\News\Category\PostDeleteEvent;
use App\Event\News\Category\PostPersistEvent;
use App\Event\News\Category\PreDeleteEvent;
use App\Event\News\Category\PrePersistEvent;
use App\Exception\CannotExecuteException;
use App\Repository\News\CategoryRepository;
use App\Service\Entity\AbstractService;
use App\Service\Entity\Traits\LoggingTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CategoryService extends AbstractService
{
    use LoggingTrait;
    public function __construct(
        private readonly CategoryRepository $repository,
        private readonly LoggerInterface $logger,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {}

    /**
     * @return Category
     */
    public function createNewEntity(): Category
    {
        return (new Category())
            ->setSort($this->repository->getNextSort())
            ->setEnable(true)
            ;
    }

    /**
     * @param Category $category
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function persistEntity(Category $category): int
    {
        $isCreate = !$category->getId();

        $preEvent = new PrePersistEvent($category, $isCreate);
        $this->eventDispatcher->dispatch($preEvent, "news_category.pre_persist");

        $this->repository->add($category);

        $postEvent = new PostPersistEvent($category, $isCreate);
        $this->eventDispatcher->dispatch($postEvent, "news_category.post_persist");

        $this->persistLog(
            "News category",
            $isCreate,
            $category->getId(),
            $category->getName()
        );
        return $isCreate ? self::CREATED : self::UPDATED;
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function canDelete(Category $category): bool
    {
        return count($category->getEntries()) === 0;
    }

    /**
     * @param Category $category
     * @return int
     * @throws CannotExecuteException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteEntity(Category $category): int
    {
        $id = $category->getId();
        if(!$this->canDelete($category)) {
            throw new CannotExecuteException("このカテゴリーは削除できません");
        }

        $preEvent = new PreDeleteEvent($category);
        $this->eventDispatcher->dispatch($preEvent, "news_category.pre_delete");

        $this->repository->remove($category);

        $postEvent = new PostDeleteEvent($category);
        $this->eventDispatcher->dispatch($postEvent, "news_category.post_delete");

        $this->deleteLog("News category", $id, $category->getName());
        return self::DELETED;
    }
}