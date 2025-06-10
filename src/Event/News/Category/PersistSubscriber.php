<?php

namespace App\Event\News\Category;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersistSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            PrePersistEvent::EVENT_NAME => 'onPrePersist',
            PostPersistEvent::EVENT_NAME => 'onPostPersist'
        ];
    }

    public function onPrePersist(PrePersistEvent $event): void
    {
        // $category = $event->getCategory();
        // $isCreated = $event->isCreate();
        // 永続化前に何か行う
    }

    public function onPostPersist(PostPersistEvent $event): void
    {
        // $category = $event->getCategory();
        // $isCreate = $event->isCreate();
        // 永続化後に何か行う
    }
}