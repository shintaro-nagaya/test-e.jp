<?php
namespace App\Utils;

use Detection\MobileDetect;

class MobileDetectUtil {
    static private ?MobileDetect $detect = null;

    static public function getDetect(): MobileDetect {
        if(self::$detect) return self::$detect;
        self::$detect = new MobileDetect;
        return self::$detect;
    }
}
