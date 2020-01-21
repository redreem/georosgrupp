<?php

class CommonModel extends AbstractModel
{
    public $id_razdel;
    public $id_okrug;

    public $search_description = '';

    public $no_nav = false;
    public $no_footer = false;

    public $db_cache_id = 'common';

    protected function dataProcess()
    {
        include_once Core::$config['application_root'] . 'common' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'CommonSQLHelper.php';
        include_once Core::$config['application_root'] . 'common' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'CommonSEOHelper.php';

        $this->no_nav = isset($_REQUEST['no_nav']);
        $this->no_footer = isset($_REQUEST['no_footer']);

    }

    public static function isMainDomain()
    {
        return trim(Application::$base_domain_short, 'www') === trim(Application::$main_domain_short, 'www');
    }

    public static function getJsLinkData($url)
    {
        $link_js_data = "onClick=openLink('" . $url . "')";
        return $link_js_data;
    }

    public static function getMainLinkData($url_path, $check_main_domain = false)
    {
        $link = Application::$main_domain . $url_path;
        if (self::isMainDomain() || !$check_main_domain) {
            $link_data = 'href="' . $link . '"';
        } else {
            $link_data = self::getJsLinkData($link);
        }
        return $link_data;
    }


}