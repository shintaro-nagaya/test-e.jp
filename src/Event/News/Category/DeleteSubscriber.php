<?php

namespace App\Event\News\Category;

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
        // $category = $event->getCategory();
        // 削除前に何か行う
    }

    public function onPostDelete(PostDeleteEvent $event): void
    {
        // $category = $event->getCategory();
        // 削除後に何か行う
    }
}