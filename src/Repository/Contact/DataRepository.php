<?php

namespace App\Repository\Contact;

use App\Entity\Contact\Data;
use App\Repository\Interfaces\AdminIndexInterface;
use App\Repository\Interfaces\InquiryRepositoryInterface;
use App\Repository\Traits\InquiryRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Data|null find($id, $lockMode = null, $lockVersion = null)
 * @method Data|null findOneBy(array $criteria, array $orderBy = null)
 * @method Data[]    findAll()
 * @method Data[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataRepository extends ServiceEntityRepository implements InquiryRepositoryInterface, AdminIndexInterface
{
    use InquiryRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Data::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Data $entity, bool $flush = true): void
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
    public function remove(Data $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param array $criteria
     * @return QueryBuilder
     */
    public function getAdminIndexQuery(array $criteria): QueryBuilder
    {
        $qb = $this->createQueryBuilder("c");
        $this->addInquiryAdminIndexQuery($qb, $criteria);
        return $qb;
    }
}
