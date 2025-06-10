<?php

namespace App\Repository\Interfaces;

use Doctrine\ORM\QueryBuilder;

interface AdminIndexInterface
{
    /**
     * 管理ページでの一覧のクエリビルダーを返す
     * @param array $criteria
     * @return QueryBuilder
     */
    public function getAdminIndexQuery(array $criteria): QueryBuilder;
}