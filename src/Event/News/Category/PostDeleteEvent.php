<?php

namespace App\Event\News\Category;

use App\Entity\News\Category;
use Symfony\Contracts\EventDispatcher\Event;

class PostDeleteEvent extends Event
{
    public const EVENT_NAME = "news_category.post_delete";
    public function __construct(private readonly Category $category) {}

    public function getCategory(): Category
    {
        return $this->category;
    }
}