<?php

class Minificator
{

    public static function process(&$content)
    {
        $nl_code_1 = chr(13) . chr(10);
        $nl_code_2 = chr(13);
        $nl_code_3 = chr(10);

        $content = str_replace($nl_code_1, '', $content);
        $content = str_replace($nl_code_2, '', $content);
        $content = str_replace($nl_code_3, '', $content);

        $content = str_replace('  ', ' ', $content);
        $content = str_replace('  ', ' ', $content);
        $content = str_replace('  ', ' ', $content);
        $content = str_replace('  ', ' ', $content);

    }
}