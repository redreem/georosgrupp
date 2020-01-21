<?php

class MobiledetectModule
{

    public static $mobile_detect;
    public static $module_root;

    public static $is_tablet = false;
    public static $is_mobile = false;
    public static $is_desktop = false;

    public static $another_method_is_tablet = false;
    public static $another_method_is_mobile = false;
    public static $another_method_is_desktop = false;

    public static function init()
    {
        self::$module_root = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR;
        require_once self::$module_root . 'Mobile_Detect.php';

    }

    public static function detect()
    {
        self::$mobile_detect = new Mobile_Detect();

        if (self::$mobile_detect->isMobile()) {

            self::another_method();
        }

        if (
            self::$mobile_detect->isTablet()
            ||
            (self::$mobile_detect->isMobile() && self::$another_method_is_tablet)
        ) {

            self::$is_tablet = true;

        } elseif (self::$mobile_detect->isMobile()) {

            self::$is_mobile = true;

        } else {

            self::$is_desktop = true;
        }
    }

    public static function another_method()
    {
        $tablet_browser = 0;
        $mobile_browser = 0;

        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {

            $tablet_browser++;
        }

        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {

            $mobile_browser++;
        }

        if (
            (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0)
            ||
            ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))
        ) {
            $mobile_browser++;
        }

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = [
            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
            'newt','noki','palm','pana','pant','phil','play','port','prox',
            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
            'wapr','webc','winw','winw','xda ','xda-'
        ];

        if (in_array($mobile_ua,$mobile_agents)) {

            $mobile_browser++;
        }

        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {

            $mobile_browser++;

            //Check for tablets on opera mini alternative headers
            $stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])
                ?
                $_SERVER['HTTP_X_OPERAMINI_PHONE_UA']
                :
                (isset($_SERVER['HTTP_DEVICE_STOCK_UA']) ? $_SERVER['HTTP_DEVICE_STOCK_UA'] : '')
            );

            if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {

                $tablet_browser++;
            }
        }

        if ($tablet_browser > 0) {
            // do something for tablet devices
            self::$another_method_is_tablet = true;

        } elseif ($mobile_browser > 0) {
            // do something for mobile devices
            self::$another_method_is_mobile = true;

        } else {
            // do something for everything else
            self::$another_method_is_desktop = true;
        }
    }
}