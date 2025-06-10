<?php

namespace App\Repository\Traits;

use App\Entity\Interfaces\InquiryInterface;
use Doctrine\ORM\QueryBuilder;
use TripleE\Utilities\QbShortcutTrait;

trait InquiryRepositoryTrait
{
    use QbShortcutTrait;
    public function isContinuePost(InquiryInterface $inquiry, string $thresholdTime = "-2 minute"): bool
    {
        $qb = $this->createQueryBuilder("i");
        $qb
            ->select('COUNT(i.id)')
            ->where($qb->expr()->eq('i.ip', ':ip'))
            ->andWhere($qb->expr()->gte("i.created_at", ":created_at"))
            ->setParameters([
                "ip" => $inquiry->getIp(),
                "created_at" => new \DateTime($thresholdTime)
            ])
            ;
        return ($qb->getQuery()->getSingleScalarResult() !== 0);
    }

    public function addInquiryAdminIndexQuery(QueryBuilder $qb, array $criteria, string $alias = "c"): QueryBuilder
    {
        $this
            ->qbsGte(
                $qb,
                $criteria,
                "send_from",
                "created_at",
                $alias
            )
            ->qbsLte(
                $qb,
                $criteria,
                "send_to",
                "created_at",
                $alias
            )
            ;
        $qb->orderBy($alias.".id", "desc");
        return $qb;
    }
}