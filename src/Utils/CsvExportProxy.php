<?php

namespace App\Utils;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use TripleE\Utilities\CsvExport;

class CsvExportProxy
{
    private CsvExport $exporter;
    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface        $logger,
        Security               $security,
        RequestStack $requestStack
    ) {
        $this->exporter = new CsvExport($entityManager, $logger, $security, $requestStack);
    }
    public function getExporter(): CsvExport
    {
        return $this->exporter;
    }
}