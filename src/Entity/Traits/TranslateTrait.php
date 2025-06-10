<?php

namespace App\Entity\Traits;

use App\Utils\LocaleUtil;

trait TranslateTrait
{
    protected function translate(string $propertyName): string
    {
        $local = LocaleUtil::$locale;
        if ($local === "ja") {
            return (string)$this->$propertyName;
        }
        $localPropertyName = $propertyName . ucfirst($local);
        if (!isset($this->$localPropertyName)) {
            return (string)$this->$propertyName;
        }
        $value = trim($this->$localPropertyName);
        if (!$value) {
            return (string)$this->$propertyName;
        }
        return (string)$this->$localPropertyName;
    }
}