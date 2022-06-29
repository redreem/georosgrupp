<?php

class DeviceDetect
{

    public static $device;
    public static $devices = [

    ];

    public static function getDeviceType()
    {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (stripos($ua, 'windows phone') != false) { // && stripos($ua,'mobile') !== false) {

            self::$device = 3;

        } elseif (stripos($ua, 'windows nt') != false) {

            $dt = 4;

        } elseif (stripos($ua, 'ipad') != false) {

            $dt = 6;

        } elseif (stripos($ua, 'iphone') != false) {

            $dt = 5;

        } elseif (stripos($ua, 'macintosh') != false) {

            $dt = 7;

        } else {

            require_once CORE_DIR . '/classes/mobile_detect/Mobile_Detect.php';
            $md = new Mobile_Detect();

            if ($md->isTablet()) {

                $dt = 2;
            } elseif ($md->isMobile()) {

                $dt = 1;
            }
        }
        if ($dt == '') {
            $dt = 0;
        }
        return $dt;
    }
}