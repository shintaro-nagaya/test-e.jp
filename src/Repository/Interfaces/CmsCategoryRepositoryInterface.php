<?php

namespace App\Repository\Interfaces;

interface CmsCategoryRepositoryInterface
{
    public function getCategoriesHaveEntry(
        string $fieldName = "entries",
        string $entryAlias = "e",
        string $targetFieldName = "Category"
    ): array;
}