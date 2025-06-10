<?php

namespace App\Entity\Traits\Cms;

use Doctrine\ORM\Mapping as ORM;
use TripleE\Utilities\ParameterBagUtil;

trait ImageTrait
{
    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $mainImage = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $mainImageWidth = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $mainImageHeight = null;

    #[ORM\Column(type: 'string', length: 128, nullable: true)]
    private ?string $thumbnail = null;

    public function getMainImage(): ?string
    {
        return $this->mainImage;
    }

    public function setMainImage(?string $mainImage): self
    {
        $this->mainImage = $mainImage;

        return $this;
    }

    public function getMainImageWidth(): ?int
    {
        return $this->mainImageWidth;
    }

    public function setMainImageWidth(?int $mainImageWidth): self
    {
        $this->mainImageWidth = $mainImageWidth;

        return $this;
    }

    public function getMainImageHeight(): ?int
    {
        return $this->mainImageHeight;
    }

    public function setMainImageHeight(?int $mainImageHeight): self
    {
        $this->mainImageHeight = $mainImageHeight;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function hasThumbnailImage(): bool
    {
        return ($this->mainImage || $this->thumbnail);
    }
    public function getThumbnailFilename(string $mailImageThumbnailPrefix = "thumb_"): string
    {
        if($this->thumbnail) return $this->thumbnail;
        if($this->mainImage) return $mailImageThumbnailPrefix. $this->mainImage;
        return "";
    }
    public function getThumbnailUrl(): string
    {
        return sprintf(
            "%s/%s/%s",
            ParameterBagUtil::$bag->get('cms_upload_dir_http'),
            $this->getUploadConfig("thumbnail")->getUploadDir(),
            $this->getThumbnailFilename()
        );
    }
    public function getImageUrl(): string
    {
        return sprintf(
            "%s/%s/%s",
            ParameterBagUtil::$bag->get('cms_upload_dir_http'),
            $this->getUploadConfig("mainImage")->getUploadDir(),
            $this->mainImage
        );
    }

    public function isImagePortrait(): bool
    {
        return ($this->mainImageWidth <= $this->mainImageHeight);
    }
}