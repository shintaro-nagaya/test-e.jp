<?php

namespace App\Repository\News;

use App\Entity\News\Category;
use App\Repository\Interfaces\AdminIndexInterface;
use App\Repository\Interfaces\CmsCategoryRepositoryInterface;
use App\Repository\Interfaces\MasterEntityRepositoryInterface;
use App\Repository\Traits\CmsCategoryRepositoryTrait;
use App\Repository\Traits\MasterEntityRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository implements MasterEntityRepositoryInterface, AdminIndexInterface, CmsCategoryRepositoryInterface
{
    use MasterEntityRepositoryTrait;
    use CmsCategoryRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Category $entity, bool $flush = true): void
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
    public function remove(Category $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

}
