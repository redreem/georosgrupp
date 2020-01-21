<?php

class OutContent
{

    static $http_codes = [
        200 => '200 OK',
        204 => '204 No Content', //нет содержимого
        206 => '206 Partial Content', //частичное содержимое
        301 => '301 Moved Permanently', //перемещено навсегда
        302 => '302 Moved Temporarily', //перемещено временно
        304 => '304 Not Modified', //не изменялось
        307 => '307 Temporary Redirect', //временное перенаправление
        308 => '308 Permanent Redirect', //постоянное перенаправление
        400 => '400 Bad Request', //плохой, неверный запрос
        401 => '401 Unauthorized', //не авторизован
        403 => '403 Forbidden', //запрещено
        404 => '404 Not Found', //не найдено
        405 => '405 Method Not Allowed', //метод не поддерживается
        410 => '410 Gone', //удалён
        500 => '500 Internal Server Error', //внутренняя ошибка сервера

    ];

    public static $content_types = [
        'html' => 'Content-type: text/html; charset=utf-8',
        'json' => 'Content-type: text/json; charset=utf-8',
    ];

    public static function execute(&$content, $type = 'html', $code = false)
    {
        if ($code && array_key_exists($code, self::$http_codes)) {
            header('HTTP/1.1 ' . self::$http_codes[$code], true);
        }

        header(self::$content_types[$type]);
        echo $content;
    }

}