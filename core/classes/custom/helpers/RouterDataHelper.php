<?php

class RouterDataHelper
{

    public static $id_item;

    public static $firm_url;
    public static $firm_discount_url;
    public static $firm_price_url;
    public static $firm_map_url;
    public static $firm_opinions_url;

    public static $okrug_domain;

    public static function getDistrict()
    {
        $result = null;

        $domain = in_array(Application::$base_domain_short, Core::$config['dev_domains']) ? Core::$config['debug_domain_emulation'] : Application::$base_domain_short;

        $district_info = Application::$db->query(

            RouterSQLHelper::sel_district(),
            [
                ':url' => '//' . $domain
            ]
        )->fetchAssocArray();

        if (!is_null($district_info)) {

            $result = $district_info;

        } else {

            if(Core::$config['debug_mode']) {

                $district_info = Application::$db->query(
                    RouterSQLHelper::sel_district_by_id(),
                    [
                        ':id_okrug' => 7
                    ]
                )->fetchAssocArray();

                $result = $district_info;

            }
        }

        if(!is_null($result)){
            ApplicationData::addData('id_okrug',        $district_info['id_okrug']);
            ApplicationData::addData('okrug_name',      $district_info['name_okrug']);
            ApplicationData::addData('okrug_name_r',    $district_info['name_r']);
            ApplicationData::addData('okrug_name_p',    $district_info['name_p']);
            ApplicationData::addData('okrug_url',       $district_info['url']);
        }

        return $result;
    }

    public static function getNetId()
    {
        $q = Application::$db->query(
            RouterSQLHelper::sel_net_id_by_alias(),
            [
                ':net_alias'    => Router::$route_spr[1],
                ':domain'       => (Core::$config['debug_mode'] ? Core::$config['debug_domain_emulation'] : Application::$base_domain_short),
            ]
        );

        if ($row = $q->fetchAssocArray()) {

            self::$id_item  = $row['id_net'];

            ApplicationData::addData('id_net', $row['id_net']);
        }
    }

    public static function getFirmId()
    {
        $q = Application::$db->query(
            RouterSQLHelper::sel_firm_id_by_alias(),
            [
                ':firm_alias' => Router::$html_alias,
            ]
        );

        if ($row = $q->fetchAssocArray()) {

            self::$id_item  = $row['ID_FIRM'];

            ApplicationData::addData('id_firm', $row['ID_FIRM']);
        }
    }

    public static function getFirmURLsByDeletedURL()
    {
        $q = Application::$db->query(
            RouterSQLHelper::sel_firm_urls_by_deleted_alias(),
            [
                ':firm_alias' => Router::$html_alias,
            ]
        );

        if ($row = $q->fetchAssocArray()) {

            self::$firm_url             = $row['FIRMA_URL'];
            self::$firm_discount_url    = $row['FIRMA_DISCOUNT_URL'];
            self::$firm_price_url       = $row['FIRMA_PRICE_URL'];
            self::$firm_map_url         = $row['FIRMA_MAP_URL'];
            self::$firm_opinions_url    = $row['URL_OTZYVY_FIRM'];
        }
    }

    public static function getNetIDByURLAndOkrug($net_alias)
    {
        $district = self::getDistrict();

        $q = Application::$db->query(
            RouterSQLHelper::sel_id_net_by_url_and_okrug(),
            [
                ':net_url_alias'    => $net_alias,
                ':id_okrug'         => $district['id_okrug']
            ]
        );

        if ($row = $q->fetchAssocArray()) {

            self::$id_item = $row['id_net'];

            ApplicationData::addData('id_net', self::$id_item);

        } else {

            if (Core::$config['debug_mode']) {

                $q = Application::$db->query(
                    RouterSQLHelper::sel_id_net_by_url(),
                    [
                        ':net_url_alias' => $net_alias,
                        ':id_okrug' => ApplicationData::getData('id_okrug'),
                    ]
                );
                if ($row = $q->fetchAssocArray()) {

                    self::$id_item = $row['id_net'];

                    ApplicationData::addData('id_net', self::$id_item);
                }
            }
        }
    }

    /**
     * @param $net_alias
     * @return array
     */
    public static function getNetIDByURLAndOkrugFromDel($net_alias)
    {
        $district = self::getDistrict();
        $result = [];
        $q = Application::$db->query(
            RouterSQLHelper::sel_net_by_url_and_okrug_from_del(),
            [
                ':net_url_alias'    => $net_alias,
                ':id_okrug'         => $district['id_okrug']
            ]
        );

        if ($row = $q->fetchAssocArray()) {
            $result = $row;
        }

        return $result;
    }

    public static function getFirmURLById($id_firm)
    {
        $q = Application::$db->query(
            RouterSQLHelper::sel_firm_url_by_id(),
            [
                ':id_firm' => $id_firm,
            ]
        );

        if ($row = $q->fetchAssocArray()) {

            $url = $row['FIRMA_URL'];
            ApplicationData::addData('firm_url', $row['FIRMA_URL']);

        } else {

            $url = '';
        }

        return $url;
    }

    /*
     * Проверка соответствия домена региону
     */
    public static function checkDomain()
    {
        $is_ignored = false;

        if (is_array(Core::$config['router']['ignored_ckeckdomains'])){
            foreach (Core::$config['router']['ignored_ckeckdomains'] as $ignored_domain){
                if (strpos(Application::$base_domain_short, $ignored_domain) !== false) {
                    $is_ignored = true;
                    break;
                }
            }
        }

        if($is_ignored){
            return true;
        }

        if (Core::$config['router']['check_domain']) {

            $q = Application::$db->query(
                RouterSQLHelper::sel_okrug_url(),
                [
                    ':id_firm' => self::$id_item,
                ]
            );

            $okrug_domain = '';

            if ($row = $q->fetchAssocArray()) {

                $okrug_domain = $row['url'];
                self::$okrug_domain = $okrug_domain;

                self::$firm_url       = $row['FIRMA_URL'];
                self::$firm_map_url   = $row['FIRMA_MAP_URL'];
                self::$firm_price_url = $row['FIRMA_PRICE_URL'];

                ApplicationData::addData('id_okrug',        $row['ID_OKRUG_FIRMA']);
                ApplicationData::addData('okrug_url',       $row['url']);
                ApplicationData::addData('firm_url',        $row['FIRMA_URL']);
                ApplicationData::addData('firm_map_url',    $row['FIRMA_MAP_URL']);
                ApplicationData::addData('id_rubr',         $row['ID_RUBR']);
                ApplicationData::addData('id_rubr_parent',  $row['ID_RUBR_PARENT']);
                ApplicationData::addData('id_rubr_2',       $row['ID_RUBR_2']);
                ApplicationData::addData('id_karta',        $row['ID_KARTA']);
                ApplicationData::addData('karta_alias',     $row['SPRAV_URL']);
                ApplicationData::addData('id_net',          $row['ID_NET']);
                ApplicationData::addData('id_gorod',        $row['ADRES_FIRM_ID_GOROD']);
                ApplicationData::addData('id_raion',        $row['ID_RAION_CITY']);
                ApplicationData::addData('okrug_name',      $row['name_1']);
                ApplicationData::addData('okrug_name_r',    $row['name_2']);
                ApplicationData::addData('okrug_name_p',    $row['name_3']);
            }

            if (Core::$config['debug_mode'] && Application::$base_domain_short == 'spr.local') {

                $res = true;
            } else {

                $res = ($okrug_domain == Application::$base_domain_short);
            }

        } else {

            $res = true;
        }

        return $res;
    }

    /**
     * Соответствие домену для сети
     * @param $id_net
     * @return bool
     */
    public static function checkDomainNet($id_net)
    {
        $is_ignored = false;

        if (is_array(Core::$config['router']['ignored_ckeckdomains'])) {
            foreach (Core::$config['router']['ignored_ckeckdomains'] as $ignored_domain) {
                if (strpos(Application::$base_domain_short, $ignored_domain) !== false) {
                    $is_ignored = true;
                    break;
                }
            }
        }

        if ($is_ignored) {
            return true;
        }

        if (Core::$config['router']['check_domain']) {
            $district = self::getDistrict();
            $id_okrug = (int)$district['id_okrug'];

            $q = Application::$db->query(
                RouterSQLHelper::sel_net_id_okrug_by_id(),
                [
                    ':id_net' => $id_net,
                ]
            );

            $net_id_okrug = 0;

            if ($row = $q->fetchAssocArray()) {
                self::$okrug_domain = $row['url'];
                $net_id_okrug = (int)$row['id_okrug'];

                ApplicationData::addData('id_okrug', $row['id_okrug']);
                ApplicationData::addData('okrug_url', $row['url']);
            }

            if (Core::$config['debug_mode'] && Application::$base_domain_short == 'spr.local') {
                $res = true;
            } else {
                $res = ($net_id_okrug === $id_okrug);
            }
        } else {
            $res = true;
        }

        return $res;
    }

    public static function getIdAndRealKartaAlias($karta_alias, $id_firm)
    {
        $real_alias = false;
        $real_id    = false;

        $q = Application::$db->query(
            RouterSQLHelper::sel_karta_url_del(),
            [
                ':alias' => $karta_alias,
            ]
        );

        if ($row = $q->fetchAssocArray()) {

            $real_id = $row['ID_POS'];

            //если есть данные, значит карта изменилась, надо взять новый алиас
            $q = Application::$db->query(
                RouterSQLHelper::sel_karta_url(),
                [
                    ':id_pos' => $real_id,
                ]
            );

            if ($row = $q->fetchAssocArray()) {

                $real_alias = $row['URL'];

                ApplicationData::addData('karta_alias',    $row['URL']);
            }

        } elseif (!$real_alias) {

            $q = Application::$db->query(
                RouterSQLHelper::sel_karta_id_pos(),
                [
                    ':alias' => $karta_alias,
                ]
            );

            $row = $q->fetchAssocArray();

            $real_id = $row['ID_POS'];

            //проверим: не изменился ли id справочника
            $q = Application::$db->query(
                RouterSQLHelper::sel_id_pos_by_id_firm(),
                [
                    ':id_firm' => $id_firm,
                ]
            );

            if ($q && $row = $q->fetchAssocArray()) {

                $id_pos_in_firm = $row['ID_KARTA'];

                ApplicationData::addData('id_karta',    $row['ID_KARTA']);

            } else {

                $id_pos_in_firm = -1;;
            }

            if ($real_id != $id_pos_in_firm) {

                $q = Application::$db->query(
                    RouterSQLHelper::sel_karta_url(),
                    [
                        ':id_pos' => $id_pos_in_firm,
                    ]
                );

                $row = $q->fetchAssocArray();

                $real_alias = $row['URL'];
                $real_id = $id_pos_in_firm;

                ApplicationData::addData('karta_alias',    $row['URL']);

            } else {

                $real_alias = $karta_alias;
            }

        } else {

            $real_alias = $karta_alias;
        }

        return [
            'id'    => $real_id,
            'alias' => $real_alias,
        ];
    }

    public static function getIdAndFirmRealAlias($firm_alias)
    {
        $q = Application::$db->query(
            RouterSQLHelper::sel_firma_url_del(),
            [
                ':alias' => $firm_alias,
            ]
        );

        if ($row = $q->fetchAssocArray()) {

            $id_firm = $row['ID_FIRM'];
            //если есть данные, значит алиас изменился, надо взять новый
            $q = Application::$db->query(
                RouterSQLHelper::sel_firma_url(),
                [
                    ':id_firm' => $id_firm,
                ]
            );

            if ($row = $q->fetchAssocArray()) {

                $real_alias = $row['URL'];

                ApplicationData::addData('firm_alias',    $row['URL']);
            } else {

                $id_firm = false;
                $real_alias = false;
            }

        } else {

            RouterDataHelper::getFirmId();

            $id_firm = self::$id_item;
            $real_alias = $firm_alias;

        }

        return [
            'id' => $id_firm,
            'alias' => $real_alias,
        ];
    }

    public static function isIdCityExist($id_net, $id_city_url){
        $q = Application::$db->query(
            RouterSQLHelper::sel_exist_city(),
            [
                ':id_net' => $id_net,
                ':id_city' => $id_city_url,
            ]
        );

        if(is_object($q)){

            $row = $q->fetchAssocArray();

            if($row['exists']){
                return true;
            }
        }

        return false;
    }

    public static function getCityFromDel($id_net, $id_city_url){
        $q = Application::$db->query(
            RouterSQLHelper::sel_city_from_del(),
            [
                ':id_net' => $id_net,
                ':id_city' => $id_city_url,
            ]
        );

        if(is_object($q)){
            $row = $q->fetchAssocArray();
            return $row;
        }

        return [];
    }

    public static function setApplicationDataFromFirm(int $id_firm)
    {
        $q = Application::$db->query(
            RouterSQLHelper::sel_firm(),
            [
                ':id_firm' => $id_firm,
            ]
        );

        if(is_object($q)){

            $row = $q->fetchAssocArray();

            if(is_array($row)){
                ApplicationData::fillUpData($row);
            }
        }
    }

    /**
     * @return string
     */
    public static function getCurrentUrl(){
        $url = Application::$base_domain . trim($_SERVER['REQUEST_URI'], '/');
        return $url;
    }
}