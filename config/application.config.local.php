<?php

return [

    'template_root'     => ROOT_DIR . 'public' . DIRECTORY_SEPARATOR  . 'template' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR,
    'application_root'  => ROOT_DIR . 'application' . DIRECTORY_SEPARATOR,

    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'collate' => 'utf-8',
        'user' => 'root',
        'password' => 'supersite123',
        'base' => 'georostgrupp',
    ],

    'minification'          => false,

    'sql' => [
        'use_cache'         => false,
        'cache_dir'         => 'cache' . DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR,
    ],

    'img' => [
        'cache_dir'         => 'prod_src' . DIRECTORY_SEPARATOR . 'img_cache' . DIRECTORY_SEPARATOR,
    ],

    'maps'=> [

    ],

    'analyt'=> [

    ],

    'adminservice_token' => '9a4f67283513de5e878bec8cb20e21fa',

];