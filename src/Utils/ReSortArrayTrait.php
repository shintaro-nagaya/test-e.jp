<?php
/**
 * CMSの様に getSort()で順番を変えるエンティティのソートを実行する
 * プレビューモードで効かなかったので実装
 */
namespace App\Utils;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait ReSortArrayTrait
{
    public function reSortArray(Collection|array $src, string $getterMethod = "getSort"): ArrayCollection {
        $tmp = [];
        foreach($src as $item) {
            if(!isset($tmp[$item->$getterMethod()])) {
                $tmp[$item->$getterMethod()] = [];
            }
            $tmp[$item->$getterMethod()][] = $item;
        }
        $dest = new ArrayCollection();
        ksort($tmp);
        foreach($tmp as $item) {
            foreach ($item as $value) {
                $dest->add($value);
            }
        }
        return $dest;
    }
}