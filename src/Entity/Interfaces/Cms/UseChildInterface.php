<?php

namespace App\Entity\Interfaces\Cms;

use Doctrine\Common\Collections\Collection;

interface UseChildInterface
{
    public function getChildren(): Collection;
    public function addChild(ChildInterface $child): self;
    public function removeChild(ChildInterface $child): self;
}