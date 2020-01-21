<?php

use Core\Errors\HttpNotFoundException;

class Application
{

    static $model;
    static $view;
    static $controller;

    static $common_model;
    static $common_view;
    static $common_controller;

    static $action = 'default';
    static $id = 0;

    static $application_name;
    static $application_path;
    static $modules_path;

    static $protocol;

    static $base_domain;
    static $base_domain_short;

    static $main_domain;
    static $main_domain_short;

    static $header_http_code = 200;

    static $router_send_abort = false;

    /**
     * @var Db
     */
    static $db;

    public static function setHttpCode($code)
    {
        self::$header_http_code = $code;
    }

    public static function redirect($url, $code = false)
    {
        if (self::$db) {

            self::$db->close();
        }

        if ($code && array_key_exists($code, OutContent::$http_codes)) {

            header('HTTP/1.1 ' . OutContent::$http_codes[$code], false);
        }

        if ($url) {

            header('Location: ' . $url);
        }
        exit();
    }

    public static function execute($error_code = false)
    {
        try {
            if (
                isset($_SERVER['HTTPS'])
                &&
                ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
                ||
                isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
                &&
                $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
            {
                self::$protocol = 'https://';

            } else {

                self::$protocol = 'http://';
            }

            self::$protocol = '//';

            self::$base_domain_short = isset($_REQUEST['domain']) ? $_REQUEST['domain'] : $_SERVER['HTTP_HOST'];
            self::$base_domain = self::$protocol . self::$base_domain_short . '/';

            self::$main_domain = self::$protocol . 'www.spr.ru/';
            self::$main_domain_short = 'www.spr.ru';

            require_once Core::$classes_path . 'Profiler.php';

            if (Core::$config['debug_mode']) {

                Profiler::$show_profile = true;
            }

            include_once Core::$classes_path . 'Db.php';
            include_once Core::$classes_path . 'Service.php';
            include_once Core::$classes_path . 'Template.php';
            include_once Core::$classes_path . 'FE.php';

            include_once Core::$classes_path . 'ApplicationData.php';
            include_once Core::$classes_path . '/helpers/ApplicationDataSQLHelper.php';

            require_once Core::$classes_path . 'Minificator.php';

            //echo '<pre>' . print_r(Core::$config, true);die();

            //self::$db = new Db();
            //self::$db->connect(Core::$config['db']);
            //self::$db->setDatabaseName(Core::$config['db']['base']);

            if (Core::$config['router']['type'] != 'default') {

                include_once Core::$classes_path . 'custom' . DIRECTORY_SEPARATOR . 'Router.php';

            } else {

                include_once Core::$classes_path . 'Router.php';
            }

            //Profiler::start_timer('Router', '');
            //$id_timer = Profiler::$id;
            if ($error_code){
                self::setHttpCode($error_code);
                Router::$route = [
                    'page404',
                ];
            } else {

                Router::routing();
            }

            if (method_exists(Router::class, 'checkAllowedParams')) {

                Router::checkAllowedParams();
            }

            if (empty($_REQUEST['ajax'])) {

                //ApplicationData::setKeyGoogleMap();
                
                // Подгружаем ключи reCAPTCHA
                //ApplicationData::setReCAPTCHAparams();
            }

            //echo Profiler::stop_timer($id_timer);die();

            if (!empty(Router::$route[1])) {

                self::$action = Router::$route[1];

            } elseif (!empty($_REQUEST['action'])) {

                self::$action = $_REQUEST['action'];

            } elseif (Router::$route[0] == 'cron') {

                self::$action  = isset($GLOBALS['argv'][2]) ? $GLOBALS['argv'][2] : 'default';
            }

            if (!empty(Router::$route[2])) {

                self::$id = Router::$route[2];

            } elseif (!empty($_REQUEST['id'])) {

                self::$id = $_REQUEST['id'];

            } elseif (Router::$route[0] == 'cron') {

                self::$id = isset($GLOBALS['argv'][3]) ? $GLOBALS['argv'][3] : 0;
            }

            if (!self::$db) {

                //self::$db = new Db();
                //self::$db->connect(Core::$config['db']);
                //self::$db->setDatabaseName(Core::$config['db']['base_old']);
            }

            self::$application_path = Core::$config['application_root'] . Router::$route[0] . DIRECTORY_SEPARATOR;
            self::$modules_path     = Core::$config['application_root'] . '_modules' . DIRECTORY_SEPARATOR;

            require_once Application::$modules_path . 'Modules.php';

            self::$application_name = ucfirst(Router::$route[0]);

            $model_name         = self::$application_name . 'Model';
            $view_name          = self::$application_name . 'View';
            $controller_name    = self::$application_name . 'Controller';

            include_once self::$application_path . $model_name . '.php';
            include_once self::$application_path . $view_name . '.php';
            include_once self::$application_path . $controller_name . '.php';

            self::$model      = new $model_name;
            self::$view       = new $view_name;
            self::$controller = new $controller_name(self::$model, self::$view);

            if (empty($_REQUEST['ajax'])) {

                self::$view->content .= FE::setFEData();

                $common_path = Core::$config['application_root'] . 'common' . DIRECTORY_SEPARATOR;

                include_once $common_path . 'CommonModel.php';
                include_once $common_path . 'CommonView.php';
                include_once $common_path . 'CommonController.php';

                self::$common_model         = new CommonModel();
                self::$common_view          = new CommonView();
                self::$common_controller    = new CommonController(self::$common_model, self::$common_view, 'default');

                if (Core::$config['minification']) {

                    Minificator::process(self::$common_view->content);
                }

                OutContent::execute(self::$common_view->content, 'html', self::$header_http_code);

            } elseif (self::$application_name != 'Cron') {

                if (empty($_REQUEST['request_data_type'])) {

                    $_REQUEST['request_data_type'] = 'html';
                }

                if (!empty($_REQUEST['fe'])) {

                    if($_REQUEST['request_data_type'] == 'html')
                    {
                        $content = &self::$view->content;
                    } else {

                        $content = json_encode([

                            'fe_data'   => FE::setFEData('json'),
                            'content'   => self::$view->content,
                        ]);
                    }

                } else {

                    $content = &self::$view->content;
                }

                OutContent::execute($content, $_REQUEST['request_data_type'], self::$header_http_code);
            }

            //self::$db->close();

        } catch (Exception $exception){

            self::exceptionHandler($exception);
        }
    }

    public static function abort($status_code = 404){

        throw new HttpNotFoundException($status_code);
    }

    private static function exceptionHandler(Exception $exception){
        $exception_class = (new \ReflectionClass($exception))->getShortName();
        switch ($exception_class) {

            case 'HttpNotFoundException':
                self::execute($exception->getCode());
                break;
            default:
                throw $exception;
        }
    }
}