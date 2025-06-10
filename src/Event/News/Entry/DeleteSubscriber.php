<?php

namespace App\Event\News\Entry;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeleteSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            PreDeleteEvent::EVENT_NAME => 'onPreDelete',
            PostDeleteEvent::EVENT_NAME => 'onPostDelete'
        ];
    }

    public function onPreDelete(PreDeleteEvent $event): void
    {
        // $entry = $event->getEntry();
        // 削除前に何かする
    }

    public function onPostDelete(PostDeleteEvent $event): void
    {
        // $entry = $event->getEntry();
        // 削除後に何かする
    }

}