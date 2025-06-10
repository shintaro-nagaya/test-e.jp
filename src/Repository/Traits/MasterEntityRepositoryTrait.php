<?php
/**
 * MasterEntityRepositoryInterfaceの実装
 */
namespace App\Repository\Traits;

use App\Entity\Interfaces\Cms\CategoryInterface;
use App\Entity\Interfaces\MasterEntityInterface;
use Doctrine\ORM\QueryBuilder;
use Throwable;
use TripleE\Utilities\EntityKeyValueUtil;
use TripleE\Utilities\QbShortcutTrait;

trait MasterEntityRepositoryTrait
{
    use QbShortcutTrait;

    /**
     * 有効なデータを取得するQBを返す
     * @return QueryBuilder
     */
    public function getMasterData(): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');
        return $this->addBaseSql($qb);
    }

    /**
     * getMasterData()の値をキーバリュー形式配列で返す
     * @param bool $doFlip ChoiceTypeに使う場合に true
     * @return array
     */
    public function getMasterKeyValueArray(bool $doFlip = true): array
    {
        return EntityKeyValueUtil::entityConvertToKeyValue($this->getMasterData()->getQuery()->getResult(), $doFlip);
    }

    /**
     * 基本のOrderBy節を設定する
     *
     * @param QueryBuilder $queryBuilder
     * @param string $mailTableAlias
     * @return QueryBuilder
     */
    public function setOrderBy(QueryBuilder $queryBuilder, string $mailTableAlias): QueryBuilder
    {
        return $queryBuilder
            ->orderBy($mailTableAlias.'.sort', 'ASC')
            ->addOrderBy($mailTableAlias.'.id', 'DESC')
            ;
    }

    /**
     * 次のソート番号を取得
     * @return int
     */
    public function getNextSort(): int
    {
        $qb = $this->createQueryBuilder('a');
        $qb
            ->select("MAX(a.sort)")
            ->andWhere('a.enable = true')
            ;
        try {
            $res = $qb->getQuery()->getSingleScalarResult();
            return $res ? $res + 1 : 1;
        } catch (Throwable) {
            return 1;
        }
    }

    /**
     * 有効なアイテムの条件とソート設定
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function addBaseSql(QueryBuilder $qb): QueryBuilder
    {
        return $this
            ->setOrderBy($qb, "a")
            ->andWhere('a.enable = true')
            ;
    }

    /**
     * 先頭のアイテムを返す
     * @return MasterEntityInterface|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getHeadItem(): MasterEntityInterface|CategoryInterface|null
    {
        $qb = $this->addBaseSql(
            $this->createQueryBuilder("a")
        )
            ->setMaxResults(1)
        ;
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * 管理画面の一覧の検索条件
     * @param array $criteria
     * @return QueryBuilder
     */
    public function getAdminIndexQuery(array $criteria): QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');
        $this->setOrderBy($qb, "c");
        $this->qbsBool(
            $qb,
            $criteria,
            "enable",
            "c"
        );
        return $qb;
    }
}