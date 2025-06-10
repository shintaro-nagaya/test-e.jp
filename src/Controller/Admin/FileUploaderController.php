<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TripleE\FileUploader\ConfigurationGetter;
use TripleE\FileUploader\Uploader;

#[Route(path: "/admin/file_upload")]
class FileUploaderController extends AbstractController
{
    #[Route(path: "/", name: "admin_file_uploader", methods: ["POST"])]
    public function upload(
        Request $request,
        ParameterBagInterface $parameterBag
    ): Response {
        $file = $request->files->get('file');
        if(empty($file)) {
            return $this->json([
                "status" => 400,
                "message" => "upload file empty"
            ]);
        }
        $entityStr = $request->request->get('entity');
        if(!$entityStr) {
            return $this->json([
                "status" => 400,
                "message" => "upload key empty"
            ]);
        }
        $config = (new ConfigurationGetter())
            ->getUploadConfigurationFromString($entityStr)
            ->setBaseUploadDir($parameterBag->get('cms_upload_dir'))
            ->setHttpLinkDir($parameterBag->get('cms_upload_dir_http'))
        ;
        try {
            $result = (new Uploader())->upload($file, $config);
            return $this->json($result->getOutputData());
        } catch (\Exception $e) {
            return $this->json([
                "status" => 500,
                "message" => $e->getMessage()
            ]);
        }
    }

    #[Route(path: "/value/{entity}/{filename}", name: "admin_file_uploader_value", methods: ["GET"])]
    public function value(
        ParameterBagInterface $parameterBag,
        string $entity,
        string $filename
    ): Response {
        $config = (new ConfigurationGetter())->getUploadConfigurationFromString($entity)
            ->setBaseUploadDir($parameterBag->get('cms_upload_dir'))
            ->setHttpLinkDir($parameterBag->get('cms_upload_dir_http'))
        ;
        return $this->json((new Uploader())->getUploadedValue($filename, $config));
    }
}