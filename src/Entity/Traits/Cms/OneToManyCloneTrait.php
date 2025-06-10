<?php

namespace App\Entity\Traits\Cms;

use Doctrine\Common\Collections\ArrayCollection;

trait OneToManyCloneTrait
{
    protected function oneToManyClone(
        string $propertyName,
        string $setSelfMethodName = "setEntry",
        ?callable $callback = null
    ): void
    {
        $collection = new ArrayCollection();
        foreach($this->$propertyName as $item) {
            $cloneEntity = clone $item;
            // 自分とのリレーションを持たせるメソッド実行
            if($setSelfMethodName) {
                $cloneEntity->$setSelfMethodName($this);
            }
            // その他追加処理するコールバック関数
            if($callback) {
                $callback($cloneEntity, $item);
            }

            $collection->add($cloneEntity);
        }
        $this->$propertyName = $collection;
    }
}