<?php

class Autoloader
{

    public static function register()
    {
        spl_autoload_extensions('.php');

        spl_autoload_register([
            'Autoloader',
            'loadCore'
        ]);

        spl_autoload_register([
            'Autoloader',
            'loadPSR4Classes'
        ]);

        spl_autoload_register([
            'Autoloader',
            'loadPSR4ClassesApplication'
        ]);
    }

    public static function loadCore($className)
    {
        $pathParts = explode('_', $className);
        $fileName = ROOT_DIR . 'core' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pathParts) . '.php';
        if (file_exists($fileName)) {
            include_once $fileName;
            return true;
        }
        return false;
    }

    public static function loadModule($className)
    {
        $pathParts = explode('_', $className);
        $fileName = ROOT_DIR . 'core' . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $pathParts) . '.php';
        include_once $fileName;
    }

    public static function loadPSR4Classes($className)
    {
        $prefix = 'Core\\';
        //make sure this is the directory with your classes
        $base_dir = __DIR__ . '/classes/';
        $len = strlen($prefix);
        if (strncmp($prefix, $className, $len) !== 0) {
            return;
        }
        $relative_class = substr($className, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }

    public static function loadPSR4ClassesApplication($className)
    {
        $prefix = 'Application\\';
        $len = strlen($prefix);
        if (strncmp($prefix, $className, $len) !== 0) {
            return;
        }
        $relative_class = substr($className, $len);
        $base_dir = Core::$config['application_root'] . 'classes' . DIRECTORY_SEPARATOR;
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
}