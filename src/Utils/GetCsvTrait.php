<?php

namespace App\Utils;

trait GetCsvTrait
{
    public function getCsv(string $csvPath): \SplFileObject
    {
        $spl = new \SplFileObject($csvPath);
        $spl->setFlags(\SplFileObject::READ_CSV | \SplFileObject::READ_AHEAD | \SplFileObject::SKIP_EMPTY);

        return $spl;
    }
}