<?php

return [

    'template_root'     => ROOT_DIR . 'public' . DIRECTORY_SEPARATOR  . 'template' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR,
    'application_root'  => ROOT_DIR . 'application' . DIRECTORY_SEPARATOR,

    'db' => [
        'host' => '192.168.12.2',
        'port' => 3306,
        'collate' => 'utf8',
        'user' => 'spr_data_prod',
        'password' => 'spr_data_prod222',
        'base' => 'georostgrupp',
    ],

    'minification'          => true,

    'sql' => [
        'use_cache'         => true,
        'cache_dir'         => 'cache' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR,
    ],

    'img' => [
        'cache_dir'         => 'prod_src' . DIRECTORY_SEPARATOR . 'img_cache' . DIRECTORY_SEPARATOR,
    ],

    'maps'=> [

    ],

    'analyt'=> [

    ],

    'adminservice_token' => 'b2cff99d33d2ce09e74605d6a18c7a0c'
];