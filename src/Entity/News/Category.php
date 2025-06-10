<?php

namespace App\Entity\News;

use App\Entity\Interfaces\Cms\CategoryInterface;
use App\Entity\Interfaces\Cms\EntryInterface;
use App\Entity\Interfaces\MasterEntityInterface;
use App\Entity\Traits\MasterEntityTrait;
use App\Entity\Traits\ModifiedTimeTrait;
use App\Repository\News\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: "cms_news_category")]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false, hardDelete: false)]
class Category implements MasterEntityInterface, CategoryInterface
{
    use SoftDeleteableEntity;
    use ModifiedTimeTrait;
    use MasterEntityTrait;

    #[ORM\OneToMany(mappedBy: 'Category', targetEntity: Entry::class)]
    private Collection $entries;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    /**
     * @return Collection<int, Entry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(EntryInterface $entry): self
    {
        if (!$this->entries->contains($entry)) {
            $this->entries[] = $entry;
            $entry->setCategory($this);
        }

        return $this;
    }

    public function removeEntry(EntryInterface $entry): self
    {
        if ($this->entries->removeElement($entry)) {
            // set the owning side to null (unless already changed)
            if ($entry->getCategory() === $this) {
                $entry->setCategory(null);
            }
        }

        return $this;
    }
}
