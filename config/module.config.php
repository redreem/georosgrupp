<?php

return [
    'module' => [

        'Mailer' => [

            'type'      => 'static',
            'charset'   => 'utf-8',
            'senders'   => [

                'red' => [
                    'email'     => 'red@spr.ru',
                    'password'  => 'Red1234',
                    'name'      => 'Red'
                ],

            ],

            'SMTPDebug' => false,
            'host'      => 'smtp.yandex.ru',

        ],

    ]
];