<?php

namespace App\Controller\Admin\Contact;

use App\Entity\Contact\Data;
use App\Form\Admin\Contact\DataSearchType;
use App\Form\Contact\DataType;
use App\Repository\Contact\DataRepository;
use App\Utils\CsvExportProxy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TripleE\Utilities\Controller\IndexListTrait;

#[Route(path: "/admin/contact/data")]
class DataController extends AbstractController
{
    use IndexListTrait;

    #[Route(
        path: "/{page}",
        name: "admin_contact_data",
        requirements: ["page" => "\d+"],
        methods: ["GET", "POST"]
    )]
    public function index(
        Request $request,
        DataRepository $repository,
        int $page = 1
    ): Response {
        $form = $this->createForm(DataSearchType::class);
        $paginate = $this->handleResumedList(
            $request,
            $form,
            $repository,
            "admin_contact_data_index",
            $page
        );
        return $this->render("admin/contact/data/index.html.twig", [
            "data" => $paginate->getPaginator()->getIterator(),
            "paginate" => $paginate,
            "form" => $form->createView(),
            "dataForm" => $this->createForm(DataType::class, new Data())->createView()
        ]);
    }

    #[Route(
        path: "/csv",
        name: "admin_contact_data_csv",
        methods: ["GET", "POST"]
    )]
    public function csv(
        Request $request,
        DataRepository $repository,
        CsvExportProxy $exportUtil
    ): Response {
        $form = $this->createForm(DataSearchType::class);
        $query = $this->handleResumedList(
            $request,
            $form,
            $repository,
            "admin_contact_data_index",
            1,
            null,
            true
        );
        return $exportUtil->getExporter()->entityCsvExport(
            $query,
            "お問い合わせ.csv"
        );
    }
}