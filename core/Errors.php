<?php

class Errors {

    static $errors = [];

    public static function init() {

        set_error_handler('Errors::OtherErrorCatcher');

        // перехват критических ошибок
        //register_shutdown_function('Errors::FatalErrorCatcher');

        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error && ($error['type'] == E_ERROR || $error['type'] == E_PARSE || $error['type'] == E_COMPILE_ERROR)) {
                if (strpos($error['message'], 'Allowed memory size') === 0) { // если кончилась память
                    ini_set('memory_limit', (intval(ini_get('memory_limit'))+64)."M"); // выделяем немножко, что бы доработать корректно

                    Errors:log("PHP Fatal: not enough memory in ".$error['file'].":".$error['line']);

                } else {

                    Errors::log("PHP Fatal: ".$error['message']." in ".$error['file'].":".$error['line']);

                }
                // ... завершаемся корректно ....
            } else {
                //Errors::log("PHP Fatal" . print_r($error, true));
            }
        });

    }

    public static function log($mess)    {
        $fh = fopen( ROOT_DIR . '/log/errors/fatal_error.log', 'a' );
        $log_str = date('d.m.Y H:i:s', time()) . ' ' . $mess . chr(13) . chr(10);
        fwrite($fh, $log_str);
        fclose($fh);
    }

/*
errno Первый аргумента errno содержит уровень ошибки в виде целого числа.
errstr Второй аргумент errstr содержит сообщение об ошибке в виде строки.
errfile Третий необязательный аргумент errfile содержит имя файла, в котором произошла ошибка, в виде строки.
errline Четвертый необязательный аргумент errline содержит номер строки, в которой произошла ошибка, в виде целого числа.
errcontext Пятый необязательный аргумент errcontext содержит массив указателей на активную таблицу символов в точке, где произошла ошибка. Другими словами, errcontext будет содержать массив всех переменных, существующих в области видимости, где произошла ошибка. Пользовательский обработчик не должен изменять этот контекст.
*/
    public static function OtherErrorCatcher($errno, $errstr, $errfile, $errline, $errcontext)    {
        // контроль ошибок:
        // - записать в лог

        /*
E_ERROR	Ошибки обычных функций (критичные ошибки)
E_WARNING	Обычные предупреждения (не критичные ошибки)
E_PARSE	Ошибки синтаксического анализатора
E_NOTICE	Замечания (аномалии в коде, возможные источники ошибок — следует отключить при наличии русского текста в коде, так как для интернациональных кодировок не обеспечивается корректная работа).
E_CORE_ERROR	Ошибки обработчика
E_CORE_WARNING	Предупреждения обработчика
E_COMPILE_ERROR	Ошибки компилятора
E_COMPILE_WARNING	Предупреждения компилятора
E_USER_ERROR	Ошибки пользователей
E_USER_WARNING	Предупреждения пользователей
E_USER_NOTICE	Уведомления пользователей
E_ALL	Все ошибки
        */

        Errors::$errors[] = [
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline,
            'errcontext' => $errcontext,
        ];
        return false;

    }

    public static function render_console() {

        $item_html = '<div class="errors_transport">';

        $cnt = 0;
        foreach (self::$errors as $id => $item) {

            $cnt++;
            $item_html .= '<span class="errors_item">';

            $item_html .= '<span class="errors_desc"><div>[' . $item['errno'] . '] ' . $item['errstr'] . ' in ' .    $item['errfile'] . ':' . $item['errline'] . '</div></span>';
            $item_html .= '<span class="errors_context" onclick="errors.infoClick(this)">context<span style="display:none">' . print_r($item['errcontext'],true) . '</span></span>';

            $item_html .= '</span>';

        }

        $item_html .= '</div>';
        if ($cnt > 0) {
            $item_html .= '<script>$(document).ready(function(){errors.alert();});</script>';
        }
        echo $item_html;

    }

}