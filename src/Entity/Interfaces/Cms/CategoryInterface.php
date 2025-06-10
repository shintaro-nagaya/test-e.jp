<?php

namespace App\Entity\Interfaces\Cms;

use Doctrine\Common\Collections\Collection;

interface CategoryInterface
{
    public function getEntries(): Collection;
    public function addEntry(EntryInterface $entry): self;
    public function removeEntry(EntryInterface $entry): self;
}