<?php

namespace App\Service\Entity\Traits;

use App\Repository\Interfaces\CmsEntryRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use TripleE\Utilities\PaginationUtil;

trait HandleIndexTrait
{
    public function handleIndex(
        Request $request,
        CmsEntryRepositoryInterface $repository,
        string $formType,
        int $defaultLimit = 16
    ): PaginationUtil
    {
        $form = $this->container->get('form.factory')->create($formType);
        $form->submit($request->query->all());
        $criteria = $form->getViewData();

        $qb = $repository->getIndexQuery($criteria);
        return new PaginationUtil(
            $qb,
            (isset($criteria["page"]))? $criteria["page"]: 1,
            (isset($criteria['limit']))? $criteria['limit']: $defaultLimit,
            [
                "criteria" => $criteria,
                "form" => $form->createView()
            ]
        );
    }
}