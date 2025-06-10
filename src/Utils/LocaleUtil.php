<?php

namespace App\Utils;

class LocaleUtil
{
    public const EN = "英語";
    public const TW = "中国語";

    static public string $locale;

    static public function setLocale(string $locale): void
    {
        self::$locale = $locale;
    }
}