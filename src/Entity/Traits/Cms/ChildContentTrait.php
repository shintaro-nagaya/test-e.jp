<?php

namespace App\Entity\Traits\Cms;

use Doctrine\ORM\Mapping as ORM;
use TripleE\Utilities\ParameterBagUtil;

trait ChildContentTrait
{
    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $headline = null;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $imageWidth = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $imageHeight = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $youtubeId = null;

    public function getHeadline(): ?string
    {
        return $this->headline;
    }

    public function setHeadline(?string $headline): self
    {
        $this->headline = $headline;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageWidth(): ?int
    {
        return $this->imageWidth;
    }

    public function setImageWidth(?int $imageWidth): self
    {
        $this->imageWidth = $imageWidth;

        return $this;
    }

    public function getImageHeight(): ?int
    {
        return $this->imageHeight;
    }

    public function setImageHeight(?int $imageHeight): self
    {
        $this->imageHeight = $imageHeight;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getYoutubeId(): ?string
    {
        return $this->youtubeId;
    }

    public function setYoutubeId(?string $youtubeId): self
    {
        $this->youtubeId = $youtubeId;

        return $this;
    }

    public function haveContent(): bool
    {
        return (
            $this->headline ||
            $this->image ||
            $this->content ||
            $this->youtubeId
        );
    }
    public function getImageUrl(): string
    {
        return sprintf(
            "%s/%s/%s",
            ParameterBagUtil::$bag->get('cms_upload_dir_http'),
            $this->getUploadConfig("image")->getUploadDir(),
            $this->image
        );
    }
    public function isImagePortrait(): bool
    {
        return ($this->imageWidth <= $this->imageHeight);
    }
}