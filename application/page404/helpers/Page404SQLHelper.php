<?php

class Page404SQLHelper
{
    public static function sql_get_opinions_cons_count()
    {
        return "
            select SUM(kolvo) as `total`
            from " . Core::$config['db']['base_old'] . ".f_cache_razdel fcr
            where fcr.id_okrug = :id_okrug and fcr.id_razdel = 1 and fcr.id_top = 1
        ";
    }

    public static function sql_get_opinions_pros_count()
    {
        return "
            select SUM(kolvo) as `total`
            from " . Core::$config['db']['base_old'] . ".f_cache_razdel fcr
            where fcr.id_okrug = :id_okrug and fcr.id_razdel = 1 and fcr.id_top = 11
        ";
    }

    public static function sql_get_opinions_count()
    {
        return "
            select SUM(kolvo) as `total`
            from " . Core::$config['db']['base_old'] . ".f_cache_razdel fcr
            where (fcr.id_okrug = :id_okrug and fcr.id_razdel = 1) and (fcr.id_top = 11 or fcr.id_top = 1)
        ";
    }

    public static function sql_get_firms_count()
    {
        return "
          select SUM(count_firm) as `total`   
          from  " . Core::$config['db']['base_old'] . ".price_cache_anons_okrug_firm pcaof
          where pcaof.id_okrug = :id_okrug
        ";
    }
}