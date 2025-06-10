<?php

namespace App\Entity\Interfaces\Cms;

interface ImageInterface
{
    public function getMainImage(): ?string;
    public function setMainImage(?string $mainImage): self;
    public function getMainImageWidth(): ?int;
    public function setMainImageWidth(?int $mainImageWidth): self;
    public function getMainImageHeight(): ?int;
    public function setMainImageHeight(?int $mainImageHeight): self;
    public function getThumbnail(): ?string;
    public function setThumbnail(?string $thumbnail): self;

    /**
     * サムネ画像かメイン画像があるか?
     * @return bool
     */
    public function hasThumbnailImage(): bool;

    /**
     * サムネイルとして使用されるファイル名を返す
     * @return string
     */
    public function getThumbnailFilename(string $mailImageThumbnailPrefix = "thumb-"): string;

    public function getThumbnailUrl(): string;

    public function getImageUrl(): string;
}