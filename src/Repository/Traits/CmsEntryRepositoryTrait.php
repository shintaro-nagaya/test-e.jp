<?php
namespace App\Repository\Traits;

use App\Entity\Interfaces\Cms\PublishDateInterface;
use App\Entity\Interfaces\Cms\UseCategoryInterface;
use Doctrine\ORM\QueryBuilder;
use TripleE\Utilities\QbShortcutTrait;

trait CmsEntryRepositoryTrait
{
    use QbShortcutTrait;

    /**
     * CmsEntryTypeTrait::addCmsEntrySearchTypes()への実装
     * @param QueryBuilder $qb
     * @param array $criteria
     * @param string $alias
     * @return $this
     */
    public function addCmsEntryAdminIndexQuery(QueryBuilder $qb, array $criteria, string $alias = "e"): self
    {
        $this
            ->qbsGte(
                $qb,
                $criteria,
                "entryDateFrom",
                "entryDate",
                $alias
            )
            ->qbsLte(
                $qb,
                $criteria,
                "entryDateTo",
                "entryDate",
                $alias
            )
            ->qbsBool(
                $qb,
                $criteria,
                "enable",
                $alias
            )
            ;
        return $this;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $criteria
     * @param string $alias
     * @param string $fieldName
     * @return $this
     */
    public function addCmsEntryAdminIndexCategoryQuery(QueryBuilder $qb, array $criteria, string $alias = "e", string $fieldName = "Category"): self
    {
        $this
            ->qbsEq(
                $qb,
                $criteria,
                $fieldName,
                $alias
            );
        return $this;
    }

    public function getIndexQueryBuilder(string $alias = "e", bool $joinCategory = true): QueryBuilder
    {
        $qb = $this->createQueryBuilder($alias);

        return $this->setPublishWhere($qb, $alias, $joinCategory);
    }

    public function setPublishWhere(QueryBuilder $qb, string $alias = "e", bool $joinCategory = true): QueryBuilder
    {
        $this
            ->addDefaultOrder($qb, $alias)
            ->andWhere($alias. ".enable = true")
        ;
        if(in_array (PublishDateInterface::class , class_implements($this->getEntityName()), true)) {
            $qb
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->isNull($alias.".publishDate"),
                        $qb->expr()->lte($alias. ".publishDate", ":currentDate")
                    )
                )
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->isNull($alias. ".closeDate"),
                        $qb->expr()->gte($alias.".closeDate", ":currentDate")
                    )
                )
                ->setParameter('currentDate', new \DateTime())
            ;
        }
        if(
            $joinCategory &&
            in_array(UseCategoryInterface::class, class_implements($this->getEntityName()), true)
        ) {
            $qb
                ->leftJoin(
                    $this->getClassMetadata()->getAssociationTargetClass("Category"),
                    "category",
                    "with",
                    $alias.".Category = category.id"
                )
                ->andWhere("category.enable = true")
            ;
        }
        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @return QueryBuilder
     */
    public function addDefaultOrder(QueryBuilder $qb, string $alias = "e"): QueryBuilder
    {
        return $qb
            ->orderBy($alias. ".entryDate", "DESC")
            ->addOrderBy($alias. ".id", "DESC")
            ;
    }
}