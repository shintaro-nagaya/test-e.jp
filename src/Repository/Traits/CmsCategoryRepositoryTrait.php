<?php

namespace App\Repository\Traits;

trait CmsCategoryRepositoryTrait
{

    /**
     * 記事をもつレコードとその記事数を返す
     * @param string $fieldName
     * @param string $entryAlias
     * @param string $targetFieldName
     * @return array
     *  [
     *      0 => CategoryInterface
     *      entry_count => int
     * ]
     */
    public function getCategoriesHaveEntry(
        string $fieldName = "entries",
        string $entryAlias = "e",
        string $targetFieldName = "Category"
    ): array
    {
        $targetSchema = $entryAlias. ".". $targetFieldName;
        $entryClass = $this->getClassMetadata()->getAssociationTargetClass($fieldName);
        $entryRepository = $this->getEntityManager()->getRepository($entryClass);
        $qb = $this->getMasterData()
            ->leftJoin($entryClass, $entryAlias, "with", "a.id = ". $targetSchema)
            ->addSelect("COUNT(".$targetSchema.") AS entry_count")
            ->groupBy($targetSchema)
        ;
        $entryRepository->setPublishWhere($qb, $entryAlias, false);

        return $qb->getQuery()->getResult();
    }
}