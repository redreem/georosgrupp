<?php

class DiscountsSQLHelper
{
    use Application\Traits\CommonSQLHelper;

    public static function sel_discount()
    {
        return "
            select 
                s.id_firm,
                s.id,
                s.date_add,
                s.date_moder,
                s.tip_skidki,
                s.zag_skidki,
                s.text_skidki,
                s.id_gold,
                s.gold,
                s.gold_skidka2,
                s.gold_skidka,
                s.procent,
                s.date_start,
                s.date_end,
                s.url_skidki,
                s.url_skidki_text,
                s.url_count_click,
                s.img_extension,
                s.id_net,
                s.id_skidki_net,
                g.sokr_gold,
                g.view_cent
            from " . Core::$config['db']['base_old'] . ".skidki s
            join " . Core::$config['db']['base_old'] . ".skidki_gold g on s.id_gold = g.id
            where s.id = :id_discount
        ";
    }

    public static function sel_firm_discounts()
    {
        return "
            select 
                s.id,
                s.id_firm,
                s.date_add,
                s.date_moder,
                s.tip_skidki,
                s.zag_skidki,
                s.text_skidki,
                s.id_gold,
                s.gold,
                s.gold_skidka2,
                s.gold_skidka,
                s.procent,
                s.url_skidki,
                s.url_skidki_text,
                s.url_count_click,
                s.img_extension,
                s.id_skidkabum,
                s.id_net,
                s.id_skidki_net,
                s.date_start,
                s.date_end,
                g.sokr_gold,
                g.view_cent,
                if(s.date_end > now(), 1, 0) as `active`
            from " . Core::$config['db']['base_old'] . ".skidki s
            join " . Core::$config['db']['base_old'] . ".skidki_gold g on s.id_gold = g.id
            where s.id_firm = :id_firm
            order by active desc, s.date_end
            limit :limit_count
        ";
    }

    public static function sel_firm_discounts_limit()
    {
        return "
            select 
                distinct  s.id,
                s.id_firm,
                s.date_add,
                s.date_moder,
                s.tip_skidki,
                s.zag_skidki,
                s.text_skidki,
                s.id_gold,
                s.gold,
                s.gold_skidka2,
                s.gold_skidka,
                s.procent,
                s.url_skidki,
                s.url_skidki_text,
                s.url_count_click,
                s.img_extension,
                s.id_skidkabum,
                s.id_net,
                s.id_skidki_net,
                s.date_start,
                s.date_end,
                g.sokr_gold,
                g.view_cent,
                (select count(s.id) from " . Core::$config['db']['base_old'] . ".skidki s where s.id_firm = :id_firm) as count
            from " . Core::$config['db']['base_old'] . ".skidki s
            join " . Core::$config['db']['base_old'] . ".skidki_gold g on s.id_gold = g.id
            where s.id_firm = :id_firm
            order by s.date_end desc
            limit :limit_count OFFSET :offset 
        ";
    }

    public static function sel_log_click()
    {
        return "
            select id_skidki
            from " . Core::$config['db']['base_old'] . ".skidki_log_click
            where id_skidki = :id_discount and ip = ':ip' and sess = ':session'
        ";
    }

    public static function ins_log_click()
    {
        return "
            insert into " . Core::$config['db']['base_old'] . ".skidki_log_click
            (id_skidki, ip, sess, id_firm)
            values (:id_discount, ':ip', ':session', ':id_firm')
        ";
    }

    public static function upd_count_click()
    {
        return "
            update " . Core::$config['db']['base_old'] . ".skidki
            set url_count_click = url_count_click + 1
            where id = :id_discount
        ";
    }

    public static function sel_net_discounts()
    {
        return "
            select 
                s.id,
                s.id_firm,
                s.date_add,
                s.date_moder,
                s.tip_skidki,
                s.zag_skidki,
                s.text_skidki,
                s.id_gold,
                s.gold,
                s.gold_skidka2,
                s.gold_skidka,
                s.procent,
                s.url_skidki,
                s.url_skidki_text,
                s.url_count_click,
                s.img_extension,
                s.id_skidkabum,
                s.id_net,
                s.id_skidki_net,
                s.date_start,
                s.date_end,
                g.sokr_gold,
                g.view_cent,
                if(s.date_end > now(), 1, 0) as `active`
            from " . Core::$config['db']['base_old'] . ".skidki s
            join " . Core::$config['db']['base_old'] . ".skidki_gold g on s.id_gold = g.id
            where s.id_net = :id_net and  s.id_skidki_net > 0
            group by s.id_skidki_net
            order by active desc, s.date_end
            limit :limit_count
        ";
    }

    public static function sel_firm_filials_discounts()
    {
        return "
            select 
                s.id,
                s.id_firm,
                s.date_add,
                s.date_moder,
                s.tip_skidki,
                s.zag_skidki,
                s.text_skidki,
                s.id_gold,
                s.gold,
                s.gold_skidka2,
                s.gold_skidka,
                s.procent,
                s.url_skidki,
                s.url_skidki_text,
                s.url_count_click,
                s.img_extension,
                s.id_skidkabum,
                s.id_net,
                s.id_skidki_net,
                s.date_start,
                s.date_end,
                g.sokr_gold,
                g.view_cent,
                fci.ADRES_FIRM address_firm,
                fci.FIRMA_DISCOUNT_URL firm_discount_url
            from " . Core::$config['db']['base_old'] . ".skidki s
            join " . Core::$config['db']['base_old'] . ".FIRMA_cache_info fci on s.id_firm = fci.id_firm
            join " . Core::$config['db']['base_old'] . ".skidki_gold g on s.id_gold = g.id
            where 
                s.id_skidki_net = :id_skidki_net
                and s.id_firm in  (" . self::sel_id_firms_by_id_net() . ")
            order by s.procent desc
        ";
    }

    public static function sel_firm_filial_first_discounts()
    {
        return "
            select * from (
            select 
                s.id,
                s.id_firm,
                s.date_add,
                s.date_moder,
                s.tip_skidki,
                s.zag_skidki,
                s.text_skidki,
                s.id_gold,
                s.gold,
                s.gold_skidka2,
                s.gold_skidka,
                s.procent,
                s.url_skidki,
                s.url_skidki_text,
                s.url_count_click,
                s.img_extension,
                s.id_skidkabum,
                s.id_net,
                s.id_skidki_net,
                s.date_start,
                s.date_end,
                g.sokr_gold,
                g.view_cent,
                fci.ADRES_FIRM address_firm,
                fci.FIRMA_DISCOUNT_URL firm_discount_url
            from " . Core::$config['db']['base_old'] . ".skidki s
            join " . Core::$config['db']['base_old'] . ".FIRMA_cache_info fci on s.id_firm = fci.id_firm
            join " . Core::$config['db']['base_old'] . ".skidki_gold g on s.id_gold = g.id
            where 
                s.id_skidki_net in (:ids_skidki_net)
                and s.id_firm in  (" . self::sel_id_firms_by_id_net() . ")
            order by s.procent desc) d
            group by d.id_skidki_net
        ";
    }

}
