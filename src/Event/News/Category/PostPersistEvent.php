<?php

namespace App\Event\News\Category;

use App\Entity\News\Category;
use Symfony\Contracts\EventDispatcher\Event;

class PostPersistEvent extends Event
{
    public const EVENT_NAME = "news_category.post_persist";
    public function __construct(
        private readonly Category $category,
        private readonly bool $createFlag
    ) {}

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function isCreate(): bool
    {
        return $this->createFlag;
    }
}