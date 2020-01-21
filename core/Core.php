<?php

class Core
{

    public static $config;
    public static $config_postfix;
    public static $config_path = ROOT_DIR . 'config' . DIRECTORY_SEPARATOR;
    public static $classes_path = ROOT_DIR . 'core' . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;

    public static $http_host;
    public static $remote_addr;
    public static $router;

    public static $user_ip;

    private static $is_prod = false;

    private static final function getCoreConfig()
    {
        self::$config = include self::$config_path . 'core' . self::$config_postfix;
    }

    private static final function getApplicationConfig()
    {

        $config_names = [
            'application',
            'module',
            'router',
        ];

        foreach ($config_names as $name) {

            $config_file = self::$config_path . $name . self::$config_postfix;

            if (file_exists($config_file)) {

                $config = include $config_file;

                foreach ($config as $key => $val) {

                    self::$config[$key] = $val;
                }
            }
        }
    }

    public static final function setConfigPostfix()
    {
        if (
            isset($GLOBALS['argv'][3])
            &&
            in_array($GLOBALS['argv'][3], [
                'local',
            ])
        ) {
                self::$config_postfix = '.config.' . $GLOBALS['argv'][3] . '.php';

        } elseif (

            $_SERVER['SERVER_ADDR'] == '127.0.0.1'
            ||
            strpos($_SERVER['HTTP_HOST'], 'local') !== false
        ) {

            self::$config_postfix = '.config.local.php';
        } else {

            self::$config_postfix = '.config.php';
        }

        if (self::$config_postfix == '.config.php') {

            self::$is_prod = true;
        }
    }

    public static final function isProdServer()
    {
        return self::$is_prod;
    }

    public static final function execute()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) {

            $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

            self::$user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $_SERVER['SERVER_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];

        } else {

            self::$user_ip = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false);
        }

        if (!isset($_SERVER['SERVER_ADDR'])) {

            $_SERVER['SERVER_ADDR'] = 'cron';
        }

        if (!isset($_SERVER['HTTP_HOST'])) {

            $_SERVER['HTTP_HOST'] = 'cron';
        }

        if (!isset($_SERVER['REQUEST_URI'])) {

            $_SERVER['REQUEST_URI'] = 'cron';
        }

        self::setConfigPostfix();
        self::getCoreConfig();

        if (self::$config['debug_mode']) {

            ini_set('error_reporting', E_ALL);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);

        } else {

            ini_set('error_reporting', 0);
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
        }

        self::getApplicationConfig();

        Application::execute();
    }


}