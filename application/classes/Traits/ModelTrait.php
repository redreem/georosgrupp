<?php

namespace Application\Traits;

use Application;
use Application\Services\Link;
use Exception;

trait ModelTrait
{
    use Constants;

    /**
     * @return Link
     */
    public function getLinkService()
    {
        if (property_exists($this, 'link_service') && is_object($this->link_service)) {
            return $this->link_service;
        } else {
            return new Link();
        }
    }

    public function cabinetLink(array $data = [])
    {
        $link_service = $this->getLinkService();
        return $link_service->getCabinetLink($data);
    }

    /**
     * All districts
     * @return array
     */
    public static function getDistricts()
    {
        $districts = [];
        $districts_info = Application::$db->query(
            CommonSQLHelper::sel_districts(),
            [],
            Application::$db->SEC_PER_DAY,
            'districts'
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
    public function getDistrictUrls($trim = false)
    {
        $district_urls = [];
        $districts = $this->getDistricts();
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