<?php

namespace App\Event\News\Entry;

use App\Entity\News\Entry;
use Symfony\Contracts\EventDispatcher\Event;

class PostPersistEvent extends Event
{
    public const EVENT_NAME = "news_entry.post_persist";

    public function __construct(
        private readonly Entry $entry,
        private readonly bool $createFlag
    ) {}

    public function getEntry(): Entry
    {
        return $this->entry;
    }

    public function isCreate(): bool
    {
        return $this->createFlag;
    }
}