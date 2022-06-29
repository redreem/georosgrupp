<?php

class ApplicationData
{

    public static $data = [
        'id_firm' => false,
        'firm_url' => false,
        'firm_alias' => false,
        'firm_map_url' => false,

        'id_net' => false,

        'id_rubr' => false,
        'id_rubr_parent' => false,
        'id_rubr_2' => false,

        'id_gorod' => false,
        'id_karta' => false,
        'karta_alias' => false,
        'id_raion' => false,

        'id_okrug' => false,
        'okrug_name' => false,
        'okrug_name_r' => false,
        'okrug_name_p' => false,
        'okrug_url' => false,

        'key_google_maps' => false,
        'key_ya_metrica' => false,
        'key_google_analyt' => false,

        'reCAPTCHA' => [
            'site_key' => '',
            'secret' => ''
        ]

    ];


    public static function addData($identifier, $data)
    {
        self::$data[$identifier] = $data;
    }

    public static function getData($identifier)
    {

        $data = isset(self::$data[$identifier]) ? self::$data[$identifier] : null;

        return $data;
    }

    public static function fillUpData(array $input_data)
    {
        $keys = array_keys(self::$data);
        foreach ($keys as $key) {
            $value = '';
            if (array_key_exists($key, $input_data)) {
                $value = $input_data[$key];
            }

            $up_key = strtoupper($key);

            if (array_key_exists($up_key, $input_data)) {
                $value = $input_data[$up_key];
            }
            if (!empty($value)) {
                self::addData($key, $value);
            }
        }
    }

    public static function setKeyGoogleMap()
    {


    }

    public static function setReCAPTCHAparams()
    {
        if (isset(Core::$config['reCAPTCHA'][Application::$base_domain_short])) {
            self::addData('reCAPTCHA', [
                'site_key' => Core::$config['reCAPTCHA'][Application::$base_domain_short]['site_key'],
                'secret' => Core::$config['reCAPTCHA'][Application::$base_domain_short]['secret']
            ]);
        } else {
            self::addData('reCAPTCHA', [
                'site_key' => 'необходимо для домена ' . Application::$base_domain_short . ' в конфиге прописать reCAPTCHA site_key',
                'secret' => 'необходимо для домена ' . Application::$base_domain_short . ' в конфиге  прописать reCAPTCHA secret'
            ]);
        }
    }
}