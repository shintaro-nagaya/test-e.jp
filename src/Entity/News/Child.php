<?php

namespace App\Entity\News;

use App\Entity\Interfaces\Cms\ChildContentInterface;
use App\Entity\Interfaces\Cms\ChildInterface;
use App\Entity\Interfaces\Cms\EntryInterface;
use App\Entity\Traits\Cms\ChildContentTrait;
use App\Entity\Traits\Cms\ChildTrait;
use App\Entity\Traits\ModifiedTimeTrait;
use App\Repository\News\ChildRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use JetBrains\PhpStorm\ArrayShape;
use TripleE\FileUploader\Configuration;
use TripleE\FileUploader\Interfaces\FileUploadEntityInterface;
use TripleE\FileUploader\Traits\FileUploadEntityTrait;

#[ORM\Entity(repositoryClass: ChildRepository::class)]
#[ORM\Table(name: "cms_news_child")]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable(fieldName: "deletedAt", timeAware: false, hardDelete: false)]
class Child implements ChildInterface, ChildContentInterface, FileUploadEntityInterface
{
    use SoftDeleteableEntity;
    use ModifiedTimeTrait;
    use ChildTrait;
    use ChildContentTrait;
    use FileUploadEntityTrait;

    #[ORM\ManyToOne(targetEntity: Entry::class, inversedBy: 'children')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Entry $Entry;

    #[ArrayShape(["image" => "\App\Service\FileUploader\Configuration"])]
    public function getUploadConfig($configName): ?Configuration
    {
        return match ($configName) {
            "image" => new Configuration(
                "news",
                [
                    "image/jpeg",
                    "image/png",
                    "image/webp"
                ],
                [
                    "orientate" => true,
                    "resize" => [2000, 2000],
                ]
            ),
            default => null,
        };
    }

    public function getEntry(): ?Entry
    {
        return $this->Entry;
    }

    public function setEntry(?EntryInterface $Entry): self
    {
        $this->Entry = $Entry;

        return $this;
    }

    /**
     * cloneされた時の処理
     * @return void
     */
    public function __clone(): void
    {
        if($this->id) {
            $this->id = null;
            // その他初期化するプロパティがあればここで
        }
    }
}
