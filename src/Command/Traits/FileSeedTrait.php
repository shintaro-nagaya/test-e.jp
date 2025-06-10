<?php

namespace App\Command\Traits;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use TripleE\FileUploader\ConfigurationGetter;

trait FileSeedTrait
{
    public function copyFiles(
        string $className,
        string $fileType,
        string $from
    ): void {
        $config = (new ConfigurationGetter())
            ->getUploadConfigurationFromEntity((new $className), $fileType)
            ->setBaseUploadDir($this->parameterBag->get('cms_upload_dir'))
            ->setHttpLinkDir($this->parameterBag->get('cms_upload_dir_http'))
        ;
        $dest = $config->getFullUploadDir();
        $finder = new Finder();
        $fs = new Filesystem();
        $finder->in($from);
        foreach($finder as $file) {
            $fs->copy($file, $dest. $file->getFilename(), true);
        }
    }
}