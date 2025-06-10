<?php

namespace App\Repository\News;

use App\Entity\News\Entry;
use App\Repository\Interfaces\AdminIndexInterface;
use App\Repository\Interfaces\CmsEntryRepositoryInterface;
use App\Repository\Traits\CmsEntryRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use TripleE\Utilities\QbShortcutTrait;
use TripleE\Utilities\Repository\CmsBeforeAfterEntryTrait;

/**
 * @method Entry|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entry|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entry[]    findAll()
 * @method Entry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntryRepository extends ServiceEntityRepository implements CmsEntryRepositoryInterface,AdminIndexInterface
{
    use QbShortcutTrait;
    use CmsEntryRepositoryTrait;
    use CmsBeforeAfterEntryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entry::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Entry $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Entry $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }



    /**
     * 管理画面一覧データ
     * @param array $criteria
     * @return QueryBuilder
     */
    public function getAdminIndexQuery(array $criteria): QueryBuilder
    {
        $qb = $this->createQueryBuilder('e');
        $this->addDefaultOrder($qb);
        $this
            ->addCmsEntryAdminIndexQuery($qb, $criteria)
            ->addCmsEntryAdminIndexCategoryQuery($qb, $criteria)
            ;

        return $qb;
    }

    /**
     * サイト一覧での記事取得条件
     * @param array $criteria
     * @param string $alias
     * @return QueryBuilder
     */
    public function getIndexQuery(array $criteria, string $alias = "e"): QueryBuilder
    {
        $qb = $this->getIndexQueryBuilder($alias);
        $this->qbsEq(
            $qb,
            $criteria,
            "Category",
            $alias
        )
        ;
        return $qb;
    }
}
