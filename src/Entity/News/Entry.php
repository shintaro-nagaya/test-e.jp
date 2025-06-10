<?php

namespace App\Entity\News;

use App\Entity\Interfaces\Cms\CategoryInterface;
use App\Entity\Interfaces\Cms\ChildInterface;
use App\Entity\Interfaces\Cms\EntryInterface;
use App\Entity\Interfaces\Cms\ImageInterface;
use App\Entity\Interfaces\Cms\LinkInterface;
use App\Entity\Interfaces\Cms\PublishDateInterface;
use App\Entity\Interfaces\Cms\UseCategoryInterface;
use App\Entity\Interfaces\Cms\UseChildInterface;
use App\Entity\Traits\Cms\EntryTrait;
use App\Entity\Traits\Cms\ImageTrait;
use App\Entity\Traits\Cms\LinkTrait;
use App\Entity\Traits\Cms\OneToManyCloneTrait;
use App\Entity\Traits\Cms\PublishDateTrait;
use App\Entity\Traits\ModifiedTimeTrait;
use App\Repository\News\EntryRepository;
use App\Utils\ReSortArrayTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use TripleE\FileUploader\Configuration;
use TripleE\FileUploader\Interfaces\FileUploadEntityInterface;
use TripleE\FileUploader\Traits\FileUploadEntityTrait;

#[ORM\Entity(repositoryClass: EntryRepository::class)]
#[ORM\Table(name: "cms_news_entry")]
#[ORM\Index(fields: ["entryDate"])]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false, hardDelete: false)]
class Entry implements EntryInterface, PublishDateInterface, ImageInterface, LinkInterface, UseCategoryInterface, UseChildInterface, FileUploadEntityInterface
{
    use SoftDeleteableEntity;
    use ModifiedTimeTrait;
    use EntryTrait;
    use PublishDateTrait;
    use ImageTrait;
    use LinkTrait;
    use FileUploadEntityTrait;
    use OneToManyCloneTrait;
    use ReSortArrayTrait;

    public function getUploadConfig(string $configName): ?Configuration
    {
        return match ($configName) {
            "mainImage" => new Configuration(
                "news",
                [
                    "image/jpeg",
                    "image/png",
                    "image/webp"
                ],
                [
                    "orientate" => true,
                    "resize" => [2000, 2000],
                    "thumb" => [600, 600]
                ]
            ),
            "thumbnail" => new Configuration(
                "news",
                [
                    "image/jpeg",
                    "image/png",
                    "image/webp"
                ],
                [
                    "orientate" => true,
                    "fit" => [600, 600]
                ]
            ),
            default => null,
        };
    }

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'entries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $Category = null;

    #[ORM\OneToMany(mappedBy: 'Entry', targetEntity: Child::class, cascade: ["persist"])]
    #[ORM\OrderBy(["sort" => "asc", "id" => "desc"])]
    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getCategory(): ?Category
    {
        return $this->Category;
    }

    public function setCategory(?CategoryInterface $Category): self
    {
        $this->Category = $Category;

        return $this;
    }

    /**
     * @return Collection<int, Child>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(ChildInterface $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setEntry($this);
        }

        return $this;
    }

    public function removeChild(ChildInterface $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getEntry() === $this) {
                $child->setEntry(null);
            }
        }

        return $this;
    }

    /**
     * 管理画面フォームからのプレビューの時に段落のソートが効かないのでここで振り直す
     * @return void
     */
    public function reSortChild(): void
    {
        $this->children = $this->reSortArray($this->children);
    }

    /**
     * cloneされた時に実行する処理
     *
     * @return void
     */
    public function __clone(): void
    {
       if($this->id) {
           // 主キーは初期化する
           $this->id = null;
           // ユニークカラムなどその他引き継がないプロパティはここで初期化

           // OneToManyでリレーションしているエンティティをcloneしていく
           $this->oneToManyClone("children");
       }
    }
}
