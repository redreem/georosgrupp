<?php

class RouterDataHelper
{

    public static $id_item;

    /**
     * @return string
     */
    public static function getCurrentUrl(){
        $url = Application::$base_domain . trim($_SERVER['REQUEST_URI'], '/');
        return $url;
    }
}