<?php

namespace App\Service\Entity\Traits;

trait LoggingTrait
{
    protected function persistLog(string $preMessage, bool $isCreate, int $id, string $name, string $postMessage = ""): void
    {
        $this->logger->info(
            sprintf(
                "%s %s [%d] %s by: %s %s",
                $preMessage,
                ($isCreate) ? "create" : "update",
                $id,
                $name,
                $this->security->getUser()->getName(),
                $postMessage
            )
        );
    }

    protected function deleteLog(string $preMessage, int $id, string $name, string $postMessage = ""): void
    {
        $this->logger->info(
            sprintf(
                "%s delete [%d] %s by %s %s",
                $preMessage,
                $id,
                $name,
                $this->security->getUser()->getName(),
                $postMessage
            )
        );
    }
}