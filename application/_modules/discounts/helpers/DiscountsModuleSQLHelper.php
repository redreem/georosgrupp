<?php

class DiscountsModuleSQLHelper
{
    use Application\Traits\CommonSQLHelper;

    public static function sel_discounts_module_for_firm()
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
            join " . Core::$config['db']['base_old'] . ".skidki_cache sc on sc.id_skidki = s.id
            where 
            ((
                sc.id_raion = :id_raion
                and (sc.id_rubr3 = :id_rubr3 or sc.id_rubr = :id_rubr3)
            )
            -- or (sc.id_raion = :id_raion)
            or (
                (sc.id_rubr3 = :id_rubr3 or sc.id_rubr = :id_rubr3)
                and sc.id_okrug = :id_okrug
            ))
            and sc.id_firm not in (:id_firms)
            group by s.zag_skidki
            order by active desc, s.date_end
            limit :limit
        ";
    }

    public static function sel_discounts_module_for_net()
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
            join " . Core::$config['db']['base_old'] . ".skidki_cache sc on sc.id_skidki = s.id
            where 
            ((
                sc.id_raion_parent = :id_raion_parent
                and sc.id_rubr = :id_rubr 
            )
            or (sc.id_raion_parent = :id_raion_parent)
            or (
                sc.id_rubr = :id_rubr 
                and sc.id_okrug = :id_okrug
            ))
            and sc.id_firm not in (:id_firms)
            and (sc.id_net != :id_net or sc.id_net is null)
            group by s.zag_skidki
            order by active desc, s.date_end
            limit :limit
        ";
    }

    public static function sel_by_okrug()
    {
        $sql = "
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
                g.view_cent
            from " . Core::$config['db']['base_old'] . ".skidki s
            join " . Core::$config['db']['base_old'] . ".skidki_gold g on s.id_gold = g.id
            join " . Core::$config['db']['base_old'] . ".skidki_cache sc on sc.id_skidki = s.id
            where 
                sc.id_okrug = :id_okrug
                and sc.id_firm not in (:id_firms)
                and (sc.id_net != :id_net or sc.id_net is null)
            group by s.zag_skidki
            order by s.date_end
            limit :limit
        ";
        return $sql;
    }

    public static function sel_by_raion()
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
            join " . Core::$config['db']['base_old'] . ".skidki_cache sc on sc.id_skidki = s.id
            where 
            sc.id_raion = :id_raion
            and sc.id_firm not in (:id_firms)
            group by s.zag_skidki
            order by active desc, s.date_end
            limit :limit
        ";
    }

    public static function sel_by_raion_and_rubric()
    {
        return "
            select 
                s.procent,
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
                s.date_start,
                s.date_end,
                s.url_skidki,
                s.url_skidki_text,
                s.img_extension,
                s.id_net,
                s.id_skidki_net, 
                s.url_count_click,
                g.sokr_gold
            from " . Core::$config['db']['base_old'] . ".skidki s
            join " . Core::$config['db']['base_old'] . ".skidki_gold g on s.id_gold = g.id
            join " . Core::$config['db']['base_old'] . ".skidki_cache c on c.id_skidki = s.id
            where 
                c.id_raion = :id_raion 
                and 
                c.id_rubr3 = :id_rubr3 
                and 
                c.id_firm <> :id_firm
            group by s.id
            order by c.tip_skidki, c.date_end desc
            limit :limit_rubric
        ";
    }

    public static function sel_by_okrug_and_rubric()
    {
        return "
            select 
                s.procent,
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
                s.date_start,
                s.date_end,
                s.url_skidki,
                s.url_skidki_text,
                s.img_extension,
                s.id_net,
                s.id_skidki_net,
                s.url_count_click ,
                g.sokr_gold
            from " . Core::$config['db']['base_old'] . ".skidki s
            join " . Core::$config['db']['base_old'] . ".skidki_gold g on s.id_gold = g.id
            join " . Core::$config['db']['base_old'] . ".skidki_cache c on c.id_skidki = s.id
            where c.id_okrug = :id_okrug and c.id_rubr3 = :id_rubr3 and c.id_firm <> :id_firm
            group by s.id
            order by c.tip_skidki, c.date_end desc
            limit :limit_rubric
        ";
    }



}