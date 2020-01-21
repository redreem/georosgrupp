<?php

return [
    'router' => [
        'type' => 'default', //core "default" router or "custom" router class
        //'type' => 'custom', //core "default" router or "custom" router class

        'default_router_appl' => [//new applications, - will be used default core router
            'page404',
            'home',
            'adminservice',
        ],

        //true type check_domain param work correctly only on production server
        'check_domain' => false,
        //'check_domain' => true,

        'allow_params' => [

        ],

        'ignored_ckeckdomains' => []
    ]
];