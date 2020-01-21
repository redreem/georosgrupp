<?php

namespace Application\Traits;

trait Constants
{
    public static $CONST = [
        'DEFAULT_TIMEZONE' => 'Europe/Moscow',
        'DEBUG_DOMAINS' => [
            'georostgrupp.local',
        ],
        'TITLES' => [
            'CLIENT' => ['клиент', 'клиента', 'клиентов'],
            'COMMENT' => ['комментарий', 'комментария', 'комментариев']
        ],
        'DISCOUNT_TYPE' => [
            'DISCOUNT' => 1,
            'COUPON' => 2,
            'OTHER' => 3,
        ],
        'ID_PROS' => 11,
        'ID_CONS' => 1,
        'ID_ASK' => 28,
        'OPINIONS' => [
            'ID_SECTION' => 1,
            'MIN_TEXT_LENGTH' => 5,
            'MAX_TEXT_LENGTH' => 5000,
            'TEXT_SPAM_LENGTH' => 30,
            'TEXT_DIFF_PERCENT' => 82,
            'MAX_HEADING_LENGTH' => 70, // Максимальная длина заголовка отзыва
        ],
        // бесплатник, полуплатник и монополист
        'PAGE_TYPE' => [
            'BESPLATNIK' => 1,
            'POLUPLATNIK' => 2,
            'MONOPOLIST' => 3,
        ]
    ];
}