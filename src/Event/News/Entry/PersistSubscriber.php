<?php

namespace App\Event\News\Entry;

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
        // $entry = $event->getEntry();
        // $isCreated = $event->isCreate();
        // 永続化前に何か行う
    }

    public function onPostPersist(PostPersistEvent $event): void
    {
        // $entry = $event->getEntry();
        // $isCreated = $event->isCreate();
        // 永続化後に何か行う
    }
}