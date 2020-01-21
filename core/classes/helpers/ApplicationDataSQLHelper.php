<?php

class ApplicationDataSQLHelper
{
    public static function sel_key_google_map()
    {
        return "
            select 
                o.key_google_maps,
                o.key_ya_metrica,
                o.key_google_analyt
            from " . Core::$config['db']['base_new'] . ".okrug_params o
            where 
                o.id_okrug = :id_okrug
        ";
    }
}