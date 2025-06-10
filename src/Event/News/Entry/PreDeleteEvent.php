<?php

namespace App\Event\News\Entry;

use App\Entity\News\Entry;
use Symfony\Contracts\EventDispatcher\Event;

class PreDeleteEvent extends Event
{
    public const EVENT_NAME = "news_entry.pre_delete";
    public function __construct(private readonly Entry $entry) {}

    public function getEntry(): Entry
    {
        return $this->entry;
    }
}