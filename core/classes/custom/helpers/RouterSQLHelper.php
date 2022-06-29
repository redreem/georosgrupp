<?php

class RouterSQLHelper
{
    public static function sel_district()
    {
        return "
            select 
                o.ID_OKRUG id_okrug, 
                o.NAME_OKRUG name_okrug, 
                o.NAME_R name_r, 
                o.NAME_P name_p,
                o.URL url
            from " . Core::$config['db']['base_old'] . ".OKRUG o
            where 
                o.URL = ':url'
        ";
    }

    public static function sel_district_by_id()
    {
        return "
            select 
                o.ID_OKRUG id_okrug, 
                o.NAME_OKRUG name_okrug, 
                o.NAME_R name_r, 
                o.NAME_P name_p,
                o.URL url
            from " . Core::$config['db']['base_old'] . ".OKRUG o
            where 
                o.id_okrug = ':id_okrug'
        ";
    }

    public static function sel_id_pos_by_id_firm()
    {
        return "
            select
                f.ID_KARTA
            from " . Core::$config['db']['base_old'] . ".FIRMA_cache_info f
            where
                f.ID_FIRM = :id_firm
        ";
    }

    public static function sel_karta_url_del()
    {
        return "
            select
                kud.ID_POS
            from " . Core::$config['db']['base_old'] . ".KARTA_URL_DEL kud
            where
                kud.URL = ':alias'
        ";
    }

    public static function sel_karta_url()
    {
        return "
            select
                ku.URL
            from " . Core::$config['db']['base_old'] . ".KARTA_URL ku
            where
                ku.ID_POS = :id_pos
        ";
    }

    public static function sel_karta_id_pos()
    {
        return "
            select
                ku.ID_POS
            from " . Core::$config['db']['base_old'] . ".KARTA_URL ku
            where
                ku.URL = ':alias'
        ";
    }

    public static function sel_firma_url_del()
    {
        return "
            select
                fud.ID_FIRM
            from " . Core::$config['db']['base_old'] . ".FIRMA_URL_DEL fud
            where
                fud.URL = ':alias'
        ";
    }

    public static function sel_firma_url()
    {
        return "
            select
                fu.URL
            from " . Core::$config['db']['base_old'] . ".FIRMA_URL fu
            where
                fu.ID_FIRM = :id_firm
        ";
    }

    public static function sel_net_id_by_alias()
    {
        return "
            select
                p.id_net
            from " . Core::$config['db']['base_old'] . ".pages_plus_url p
            join " . Core::$config['db']['base_new'] . ".okrug_params o on o.url = ':domain' and o.id_okrug = p.id_okrug
            where
                p.url = ':net_alias'
        ";
    }

    public static function sel_firm_url_by_id()
    {
        return "
            select
                f.FIRMA_URL
            from " . Core::$config['db']['base_old'] . ".FIRMA_cache_info f
            where
                f.ID_FIRM = :id_firm
        ";
    }

    public static function sel_firm_id_by_alias()
    {
        return "
            select
                f.ID_FIRM
            from " . Core::$config['db']['base_old'] . ".FIRMA_URL f
            where
                f.URL = ':firm_alias'
        ";
    }

    public static function sel_firm_urls_by_deleted_alias()
    {
        return "
            select
                f.FIRMA_URL,
                f.FIRMA_DISCOUNT_URL,
                f.FIRMA_PRICE_URL,
                f.FIRMA_MAP_URL,
                f.FIRMA_VIDEO_URL,
                f.URL_OTZYVY_FIRM,
                f.RUBR_URL
            from " . Core::$config['db']['base_old'] . ".FIRMA_URL_DEL ud
            join " . Core::$config['db']['base_old'] . ".FIRMA_cache_info f on f.ID_FIRM = ud.ID_FIRM
            where
                ud.URL = ':firm_alias'
        ";
    }

    public static function sel_id_net_by_url_and_okrug()
    {
        return "
            select
                p.id_net
            from " . Core::$config['db']['base_old'] . ".pages_plus_url p
            where
                p.url = ':net_url_alias'
                and p.id_okrug = :id_okrug
        ";
    }

    public static function sel_net_by_url_and_okrug_from_del()
    {
        return "
            select
                ppu_del.id_net,
	            ppu.url
            from " . Core::$config['db']['base_old'] . ".pages_plus_url_del ppu_del
            inner join " . Core::$config['db']['base_old'] . ".pages_plus_url ppu on ppu.id_net = ppu_del.id_net
            where
                ppu_del.url = ':net_url_alias'
                and ppu_del.id_okrug = :id_okrug
        ";
    }

    public static function sel_city_from_del()
    {
        return "
            select
                ppuc_del.id_net,
	            ppuc.url
            from " . Core::$config['db']['base_old'] . ".pages_plus_url_city_del ppuc_del
            inner join " . Core::$config['db']['base_old'] . ".pages_plus_url_city ppuc ON ppuc.id_net = ppuc_del.id_net and ppuc.id_karta = ppuc_del.id_karta
            where
                ppuc_del.url = ':id_city'
                and ppuc_del.id_net = ':id_net'
        ";
    }

    public static function sel_id_net_by_url()
    {
        return "
            select
                p.id_net
            from " . Core::$config['db']['base_old'] . ".pages_plus_url p
            where
                p.url = ':net_url_alias'
                and
                p.id_okrug = :id_okrug
        ";
    }

    public static function sel_firm_id_by_net_url_alias()
    {
        return "
            select
                f.ID_FIRM
            from " . Core::$config['db']['base_old'] . ".FIRMA_cache_info f
            where
                f.URL_SETI_MAPS = '//www.spr.ru/map/:net_url_alias/'
        ";
    }

    public static function sel_okrug_url()
    {
        return "
            select
                op.url,
                op.name_1,
                op.name_2,
                op.name_3,
                f.FIRMA_URL,
                f.ID_OKRUG_FIRMA,
                f.FIRMA_MAP_URL,
                f.ID_RUBR,
                f.ID_RUBR_PARENT,
                f.ID_RUBR_2,
                f.ID_KARTA,
                f.SPRAV_URL,
                f.ID_NET,
                f.ADRES_FIRM_ID_GOROD,
                f.ID_RAION_CITY,
                f.FIRMA_PRICE_URL
            from " . Core::$config['db']['base_old'] . ".FIRMA_cache_info f
            join " . Core::$config['db']['base_new'] . ".okrug_params op on op.id_okrug = f.ID_OKRUG_FIRMA
            where
                f.ID_FIRM = :id_firm
        ";
    }

    public static function sel_exist_city()
    {
        return "
            select 
                exists(
                    select 1
                    from " . Core::$config['db']['base_old'] . ".pages_plus_url_city p
                    where
                        p.url = ':id_city' 
                        and 
                        p.id_net = :id_net 
                    limit 1
            ) `exists`
        ";
    }

    public static function sel_firm()
    {
        return "
            select
                f.ID_FIRM id_firm,
                f.ID_RUBR,
                f.ID_OKRUG_FIRMA,
                f.ID_KARTA,
                f.ID_KARTA id_raion,
                f.ADRES_FIRM_ID_GOROD id_gorod,
                f.FIRMA_URL firm_url,
                f.ID_RUBR_PARENT id_rubr_parent,
                f.ID_RUBR_2 id_rubr_2
            from " . Core::$config['db']['base_old'] . ".FIRMA_cache_info f
            where
              f.ID_FIRM = :id_firm
        ";
    }

    public static function sel_net_id_okrug_by_id()
    {
        return "
            select
                p.id_net,
                p.id_okrug,
                o.url
            from " . Core::$config['db']['base_old'] . ".pages_plus_url p
            join " . Core::$config['db']['base_new'] . ".okrug_params o on o.id_okrug = p.id_okrug
            where
                p.id_net = ':id_net'
        ";
    }
}