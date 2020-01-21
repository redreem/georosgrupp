<?php

class Module
{

    public static function load($module_name)
    {
        $up_first_name = ucfirst($module_name);
        include_once ROOT_DIR . 'core' . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . $up_first_name . DIRECTORY_SEPARATOR . $up_first_name . 'Module.php';
    }

}