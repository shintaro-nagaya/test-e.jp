<?php
/**
 * マスタのEntityが持つべきメソッドを定義
 */
namespace App\Repository\Interfaces;

use App\Entity\Interfaces\Cms\CategoryInterface;
use App\Entity\Interfaces\MasterEntityInterface;
use Doctrine\ORM\QueryBuilder;

interface MasterEntityRepositoryInterface
{
    /**
     * マスターデータを取得するためのメソッド
     * @return QueryBuilder
     */
    public function getMasterData(): QueryBuilder;

    /**
     * 現在ある順番の数字の次のものを取得
     * @return int
     */
    public function getNextSort(): int;

    /**
     * 有効なアイテムの条件とソートを設定
     * @param QueryBuilder $qb
     * @return QueryBuilder
     */
    public function addBaseSql(QueryBuilder $qb): QueryBuilder;

    /**
     * 初期選択として有効な先頭にあるアイテムを返す
     * @return MasterEntityInterface
     */
    public function getHeadItem(): MasterEntityInterface|CategoryInterface|null;
}