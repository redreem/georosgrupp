<?php

require_once Core::$config['application_root'] . '_modules' . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'CommonModuleSQLHelper.php';

class CommonModuleModel extends ModulesModel
{
    public static $db_cache_id = 'modules/common';

    /**
     * Info about district(okrug)
     * if url is in not allowed urls then result will be for district of main_domain
     * @return array|null
     */
    public static function getDistrict()
    {
        $result = null;

        $district_urls = self::getDistrictUrls();

        $search_url = '//' . Application::$base_domain_short;
        if (!in_array($search_url, $district_urls)) {
            $search_url = '//' . Application::$main_domain_short;
        }

        $district_info = Application::$db->query(
            CommonModuleSQLHelper::sel_district(),
            [
                ':url' => $search_url
            ],
            Application::$db->SEC_PER_DAY,
            self::$db_cache_id
        )->fetchAssocArray();

        if (!is_null($district_info)) {
            $result = $district_info;
        }
        return $result;
    }

    /**
     * All districts
     * @return array
     */
    public static function getDistricts()
    {
        $districts = [];
        $districts_info = Application::$db->query(
            CommonModuleSQLHelper::sel_districts(),
            [],
            Application::$db->SEC_PER_DAY,
            self::$db_cache_id
        );

        while ($row = $districts_info->fetchAssocArray()) {
            $districts[] = $row;
        }
        return $districts;
    }

    /**
     * Array of all district's url
     * @param bool $trim
     * @return array
     */
    public static function getDistrictUrls($trim = false)
    {
        $district_urls = [];
        $districts = self::getDistricts();
        if (count($districts) > 0) {
            $district_urls = array_column($districts, 'url');
            // remove www and //
            if ($trim) {
                $district_urls = array_map(function ($url) {
                    $url = trim($url, '/');
                    $url = trim($url, 'www.');
                    return $url;
                }, $district_urls);
            }
        }
        return $district_urls;
    }
}