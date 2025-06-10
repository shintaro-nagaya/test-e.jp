<?php
/**
 * CMS記事データのリポジトリインターフェース
 */
namespace App\Repository\Interfaces;

use Doctrine\ORM\QueryBuilder;

interface CmsEntryRepositoryInterface
{
    /**
     * @param QueryBuilder $qb
     * @param string $alias
     * @return QueryBuilder
     */
    public function addDefaultOrder(QueryBuilder $qb, string $alias = "e"): QueryBuilder;

    /**
     * サイト側の一覧取得用QB
     * @param array $criteria
     * @param string $alias
     * @return QueryBuilder
     */
    public function getIndexQuery(array $criteria, string $alias = "e"): QueryBuilder;
}