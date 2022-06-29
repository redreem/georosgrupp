<?php

class CommonModuleSQLHelper
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