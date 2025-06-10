<?php

namespace App\Service\Entity;

use Psr\Container\ContainerInterface;

abstract class AbstractService
{
    public const CREATED = 1;
    public const UPDATED = 2;
    public const DELETED = 3;

    protected ContainerInterface $container;

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }
}