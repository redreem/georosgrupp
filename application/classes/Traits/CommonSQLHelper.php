<?php

namespace Application\Traits;

use Core;

trait CommonSQLHelper
{
    public static function sel_net_info_by_id()
    {
        return "
            select 
                p.id,
                p.id id_net,
                p.var,
                p.name_net,
                p.id_okrug,
                p.id_rubr,
                p.id_karta_parent,
                p.logotip_extension `logo_extension`,
                p.datechange `confirmed_date`,
                p.dateadd,
                ppu.url net_url,
                (" . self::sel_net_opinions_cons_count() . ")  net_opinions_cons_count,
                (" . self::sel_net_opinions_pros_count() . ")  net_opinions_pros_count,
                (" . self::sel_net_count() . ")  net_total_count,
                g.name_gold currency_name,
                g.sokr_gold currency_title,
                g.view_cent float_price
            from " . Core::$config['db']['base_old'] . ".pages_net p
            left join " . Core::$config['db']['base_old'] . ".pages_plus_url ppu on ppu.id_net = p.id
            left join " . Core::$config['db']['base_old'] . ".OKRUG o on o.ID_OKRUG = ppu.id_okrug
            left join " . Core::$config['db']['base_old'] . ".skidki_gold g on o.valuta = g.id
            where 
                p.id = :id_net
        ";
    }

    // net_opinions_cons_count
    public static function sel_net_opinions_cons_count()
    {
        return "
            select
                count(f.id_firm) 
            from " . Core::$config['db']['base_old'] . ".f_cache_forum_all f
            join " . Core::$config['db']['base_old'] . ".pages_net_link p on p.id_firm = f.id_firm
            where
                f.id_top = 1 and p.id_net = :id_net
        ";
    }

    // net_opinions_pros_count
    public static function sel_net_opinions_pros_count()
    {
        return "
            select
                count(f.id_firm) 
            from " . Core::$config['db']['base_old'] . ".f_cache_forum_all f
            join " . Core::$config['db']['base_old'] . ".pages_net_link p on p.id_firm = f.id_firm
            where
                f.id_top = 11 and p.id_net = :id_net
        ";
    }

    // total count net
    public static function sel_net_count()
    {
        return "
            select
                count(pnl.id_net) as total
            from " . Core::$config['db']['base_old'] . ".pages_net_link pnl
            where
                pnl.id_net = :id_net
        ";
    }

    public static function sel_id_firms_by_id_net()
    {
        return "
            select 
                pnl.id_firm
            from " . Core::$config['db']['base_old'] . ".pages_net_link pnl
            where 
                pnl.id_net = :id_net
        ";
    }

    public static function sel_firm_by_id()
    {
        return "
            select
                f.ID_FIRM id,
                f.FIRMNAME `name`,
                f.FIRMA_OPISANIE firm_description,
                f.FIRMNAME_ONLY,
                f.ID_RUBR,
                f.ID_RUBR_2_URL,
                f.ID_RUBR_2_URL id_rubr_2_url,
                f.RUBR_NAME,
                f.RUBR_URL rubr_url,
                r.URL parent_rubr_url,
                f.ID_OKRUG_FIRMA,
                f.ID_KARTA,
                f.NAME_OKRUG_FIRMA,
                f.SPRAV_NAME_P,
                f.SPRAV_NAME sprav_name,
                o.NAME_P OKRUG_NAME_P,
                if (r.PR_NAME1 is not NULL, r.PR_NAME1, r.PR_NAME) as rubr_name,
                f.ADRES_FIRM address,
                f.ADRES_FIRM_DOP,
                f.ADRES_FIRM_DOP address_additional,
                f.ADRES_FIRM_SOCR address_abbreviated,
                f.ADRES_FIRM_ID_GOROD id_gorod,
                f.SPRAV_URL sprav_url,
                f.URL_OKRUG_FIRMA,
                f.COUNT_SETI_TOTAL net_opinions_count,
                f.COUNT_SETI net_filials_count,
                f.COUNT_SETI_POLOGIT net_opinions_pros_count,
                f.COUNT_SETI_OTRICAT net_opinions_cons_count,
                f.URL_SETI_OTZYVY net_opinions_url,
                f.COUNT_VOPROS,
                f.COUNT_VOPROS opinions_asks_count,
                f.FIRMA_URL firm_url,
                f.ID_NET id_net,
                f.URL_SETI_MAPS net_map_url,
                f.FIRMA_MAP_URL,
                f.FIRMA_MAP_URL firm_map_url,
                f.FIRMA_PRICE_URL firm_price_url,
                f.URL_OTZYVY_FIRM,
                f.URL_OTZYVY_FIRM firm_opinion_url,
                f.FIRMA_VIDEO_URL,
                f.FIRMA_VIDEO_URL firm_story_url,
                f.FIRMA_DISCOUNT_URL,
                f.FIRMA_DISCOUNT_URL firm_url_discounts,
                f.FIRM_LOGO,
                f.FIRMA_SITE,
                f.FIRMA_SITE_SPLIT,
                f.FIRMA_SITE2,
                f.FIRMA_SITE_SPLIT2,
                f.ZAG_SETI_ADRES,
                f.URL_SETI_MAPS,
                f.FIRM_LOGO_WIDTH,
                f.FIRM_LOGO_HEIGHT,
                f.COUNT_POLOGIT opinions_pros_count,
                f.COUNT_OTRICAT opinions_cons_count,
                f.COUNT_OTZYV_TOTAL opinions_count,
                f.DATECHANGE_FIRMA `confirmed_date`,
                f.FIRMA_OPISANIE,
                f.NEW_TITLE_OTZYV new_title_opinion,
                f.GMT gmt,
                f.PARTNER_ID partner_id,
                f.PARTNER_BUTTON partner_button,
                f.PARTNER_BUTTON_TEXT partner_button_text,
                f.PARTNER_TIP_BUTTON partner_tip_button,
                f.PARTNER_URL_BANNER partner_url_banner,
                f.PARTNER_URL partner_url,
                f.PARTNER_TIP_URL partner_tip_url,
                ku.URL karta_url,
                ppu.zag_otzyvy,
                ppu.zag_otzyvy opinions_title,
                p.id page_id,
                p.plata,
                (select count(id) from " . Core::$config['db']['base_old'] . ".price_cache_item where id_firm = f.ID_FIRM) count_prices,
                (select count(id) from " . Core::$config['db']['base_old'] . ".skidki where id_firm = f.ID_FIRM) discounts_count,
                (select count(id) from " . Core::$config['db']['base_new'] . ".stories where firm_id = f.ID_FIRM) stories_count,
                g.name_gold currency_name,
                g.sokr_gold currency_title,
                g.view_cent float_price,
                k_level_3.`name` oblast,
                k_level_4.`name` country
            from " . Core::$config['db']['base_old'] . ".FIRMA_cache_info f
            left join " . Core::$config['db']['base_old'] . ".RUBR r on r.PR_ID = f.ID_RUBR
            left join " . Core::$config['db']['base_old'] . ".KARTA_URL ku on ku.ID_POS = f.ID_KARTA
            left join " . Core::$config['db']['base_old'] . ".OKRUG o on o.ID_OKRUG = f.ID_OKRUG_FIRMA
            left join " . Core::$config['db']['base_old'] . ".pages_plus_url ppu on ppu.id_firm = f.ID_FIRM
            left join " . Core::$config['db']['base_old'] . ".skidki_gold g on f.ID_VALUTA = g.id
            left join " . Core::$config['db']['base_old'] . ".pages p on f.ID_FIRM = p.id_firm 
            left join " . Core::$config['db']['base_old'] . ".KARTA k_level_1 ON k_level_1.ID_POS = f.ADRES_FIRM_ID_GOROD
            left join " . Core::$config['db']['base_old'] . ".KARTA k_level_2 ON  k_level_2.ID_POS = k_level_1.ID_PARENT
            left join " . Core::$config['db']['base_old'] . ".KARTA k_level_3 ON  k_level_3.ID_POS = k_level_2.ID_PARENT
            left join " . Core::$config['db']['base_old'] . ".KARTA k_level_4 ON  k_level_4.ID_POS = k_level_3.ID_PARENT
            where
                f.ID_FIRM = :id_firm
        ";
    }

    public static function sel_districts()
    {
        return "
            select 
                o.ID_OKRUG id_okrug,
                o.NAME_OKRUG name_okrug,
                o.NAME_R name_r,
                o.NAME_P name_p,
                o.URL url
            from " . Core::$config['db']['base_old'] . ".OKRUG o
            where 1
        ";
    }
}