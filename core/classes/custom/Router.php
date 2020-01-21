<?php

class Router
{

    public static $route = [];
    public static $route_spr = [];

    public static $uri_params_index;
    public static $html_index;
    public static $hash_index;

    public static $html_alias;
    public static $request_uri;

    /**
     * Кастомный роутинг для СПР.
     * Разбирает адрес и формирует массив $route, понятный приложению (controller, action, id)
     */
    public static function routing()
    {
        require_once Core::$classes_path . 'custom' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'RouterDataHelper.php';
        require_once Core::$classes_path . 'custom' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'RouterSQLHelper.php';

        $request_uri = trim(str_replace('?', '/?', $_SERVER['REQUEST_URI']), '/');
        $request_uri = preg_replace('|/{2,}|','/',$request_uri);
        self::$request_uri = $request_uri;

        self::$uri_params_index = strpos($request_uri, '?');
        self::$html_index       = strpos($request_uri, '.html');
        self::$hash_index       = strpos($request_uri, '#');

        if (self::$uri_params_index) {
            $route = substr($request_uri, 0, self::$uri_params_index - 1);
        }

        self::$route_spr = explode('/', (!isset($route) ? $request_uri : $route));

        self::$route_spr = array_filter(self::$route_spr);

        //ignore `page` segment
        if (!empty(self::$route_spr) && self::$route_spr[0] === 'page'){
            array_shift(self::$route_spr);
        }

        //получим алиас html-файла, если он есть
        if (self::$html_index) {
            $last_slash_index = strrpos($request_uri, '/');
            self::$html_alias = substr($request_uri, $last_slash_index + 1, self::$html_index - $last_slash_index - 1);
        }

        if (empty(self::$route_spr[0])) {

            self::$route = [
                Core::$config['default_controller']
            ];

        } else {

            $name_main_route = ucfirst(self::$route_spr[0]);
            $default_route = "route{$name_main_route}Default";

            if (method_exists(self::class, $default_route)) {

                self::$default_route();

            } elseif (self::$route_spr[0] == 'screenshotmap') {//ajax запросы независящие от роутера

                 self::$route = ['screenshotmap'];

            } elseif (self::$route_spr[0] == 'discount') {//URL скидок

                self::routeDiscounts();

            } elseif (self::$route_spr[0] == 'map' && !isset($_REQUEST['id_map'])) {//URL схемы проезда

                if (!empty(self::$route_spr[1]) && !empty(self::$route_spr[2])) {//схема проезда - на map

                    // здесь могут быть два типа (map или division)
                    // но сначала проверяем для division
                    if(!self::routeDivision()){

                        self::routeMap();
                    }

                } else {

                    self::routeDivision();
                }

            } elseif (self::$route_spr[0] == 'otzyvy') {//URL отзывов

                self::routeOpinions();

            } elseif (self::$route_spr[0] == 'all') {//URL каталога

                //еще нет реализации приложения

            } elseif (self::$route_spr[0] == 'prices' && !isset($_REQUEST['id_price'])) {//URL прасов

                self::routePrices();

            } elseif (self::$route_spr[0] == 'business') {

                if (isset(self::$route_spr[1]) && self::$route_spr[1] != 'reputation' && self::$route_spr[1] != 'monopolist') {

                    return self::page404();

                } else {

                    self::routeNewURL();
                }

            } elseif (self::$html_index || self::$route_spr[0] == 'view.php') {//URL фирмы

                self::routeFirm();

            } elseif (in_array(self::$route_spr[0], Core::$config['router']['default_router_appl'])) {//новый урл
                self::routeNewURL();
            } else {
                return self::page404();
            }
        }
    }

    /**
     * Check and filter request params
     * @throws \Core\Errors\HttpNotFoundException
     */
    public static function checkAllowedParams()
    {
        $request_method = self::getRequestMethod();
        $allow_params_config = Core::$config['router']['allow_params'];

        $type = 'direct';

        if (!empty($_REQUEST['ajax']) || $request_method !== 'get') {
            $type = 'ajax';
        }

        $current_route = '';
        if (count(self::$route) > 0) {
            $current_route = self::$route[0];
        }

        $request_params_ignore = ['urlpath', 'html'];

        $request_params = array_filter($_REQUEST, function ($k) use ($request_params_ignore) {
            return !in_array($k, $request_params_ignore);
        }, ARRAY_FILTER_USE_KEY);

        if (array_key_exists($current_route, $allow_params_config)) {
            $allow_params = $allow_params_config[$current_route][$type] ?? [];

            if(empty($allow_params)){
                return;
            }

            $need_redirect = false;
            $need_error = false;

            $request_params_original = $request_params;

            // check types
            foreach ($request_params as $key_param => $request_param) {

                if (array_key_exists($key_param, $allow_params)) {
                    $param_info = $allow_params[$key_param];
                    $type = self::getTypeFromString($request_param);

                    if(!is_array($param_info['type'])){
                        $param_info['type'] = [$param_info['type']];
                    }
                    if (!in_array($type, $param_info['type'])) {
                        if ($param_info['required']) {
                            $need_error = true;
                            break;
                        } else {
                            unset($request_params[$key_param]);
                        }
                    }else{
                        // check allowed_values
                        if (array_key_exists('allowed_values', $param_info)){
                            $allowed_values = $param_info['allowed_values'];
                            if(!is_array($allowed_values)){
                                $allowed_values = [$allowed_values];
                            }

                            if(!in_array($request_param, $allowed_values)){
                                $need_error = true;
                                break;
                            }
                        }
                    }
                }
            }

            $list_allow_key_params = array_keys($allow_params);
            $list_request_key_params = array_keys($request_params);

            $diff_params = array_diff($list_request_key_params, $list_allow_key_params);

            // exist some extra params
            if (count($diff_params) || count($request_params) !== count($request_params_original)) {
                $need_redirect = true;
            }

            if ($need_error) {
                Application::abort(410);
            }

            // remove params and redirect
            if ($need_redirect) {
                $url = Application::$base_domain . trim($_SERVER['REQUEST_URI'], '/');
                $url_info = parse_url($url);
                $query_array = $request_params;

                $correct_params = array_filter($query_array, function ($k) use ($diff_params) {
                    return !in_array($k, $diff_params);
                }, ARRAY_FILTER_USE_KEY);

                $new_query = http_build_query($correct_params);
                $url_info['query'] = $new_query;

                $new_url = self::build_url($url_info);
                Application::redirect($new_url, 301);
            }
        }

        //TODO убрать после внедрения округа во все запросы роутинга
        // add info about okrug if it's not still filled
        if (!ApplicationData::getData('id_okrug')) {
            RouterDataHelper::getDistrict();
        }
    }

    /**
     * @param string $param
     * @return null|string
     */
    public static function getRequestParam(string $param)
    {
        $result = $_REQUEST[$param] ?? null;
        return $result ? trim($result) : $result;
    }

    /**
     * @return string
     */
    private static function getRequestMethod()
    {
        $request_method = strtolower(trim($_SERVER['REQUEST_METHOD']));
        return $request_method;
    }

    /**
     * Net's group default router
     */
    private static function routeNetDefault(){
        $segments = self::$route_spr;
        $net_pages = ['prices', 'discounts', 'otzyvy'];

        array_shift($segments); // without 'net'

        # route /net/
        if(count($segments) === 0){
            return self::page404();
        }

        # route /net/net_alias - филиалы
        if(count($segments) === 1){
            $net_alias = $segments[0];
            return self::routeDivision();
        }

        # route /net/net_alias/page - страница сети или филиалы в городе
        if(count($segments) === 2){
            $net_alias = $segments[0];
            $page = $segments[1];

            if(in_array($page, $net_pages)){
                if($page === 'prices'){
                    return self::routePricesNet($net_alias);
                }
                if($page === 'discounts'){
                    return self::routeDiscountsNet($net_alias);
                }
            }else{
                // филиалы в городе
                return self::routeDivision();
            }
        }

        return self::page404();
    }

    /**
     * Division
     * pages like:
     * division/?id_net=1641
     * division/mcdonalds
     */
    private static function routeDivisionDefault()
    {
        return self::routeDivision();
    }


    /**
     * Statistics
     */
    private static function routeStatisticsDefault()
    {
        $id_firm = $_REQUEST['id_firm'] ?? 0;
        $id_net = $_REQUEST['id_net'] ?? 0;

        self::$route = [
            'statistics',
        ];

        if($id_firm > 0){
            RouterDataHelper::$id_item = $id_firm;
            if(!RouterDataHelper::checkDomain()){
                $okrug_domain = trim(RouterDataHelper::$okrug_domain, '/');

                $url = "//{$okrug_domain}/statistics?id_firm={$id_firm}";
                Application::redirect($url, 301);
            }
        }

        if($id_net > 0){
            if(!RouterDataHelper::checkDomainNet($id_net)){
                $okrug_domain = trim(RouterDataHelper::$okrug_domain, '/');

                $url = "//{$okrug_domain}/statistics?id_net={$id_net}";
                Application::redirect($url, 301);
            }
        }

        return true;
    }

    private static function routeDiscounts()
    {
        //https://www.spr.ru/discount/santehnika-5042661.html#371344
        //http://spr.local/discount/medstayl-effekt.html

        if (!self::$html_alias) {

            self::page404();

        } else {

            RouterDataHelper::getFirmId();

            if (RouterDataHelper::$id_item) {

                if (RouterDataHelper::checkDomain()) {

                    self::$route = [
                        'discounts',
                    ];

                    $_REQUEST['id_firm'] = RouterDataHelper::$id_item;

                } else {

                    Application::redirect(RouterDataHelper::$firm_discount_url, 301);
                }

            } else {

                //проверим нет ли урла в удаленных
                RouterDataHelper::getFirmURLsByDeletedURL();

                if (RouterDataHelper::$firm_url) {

                    Application::redirect(RouterDataHelper::$firm_discount_url, 301);

                } else {

                    self::page404();
                }
            }
        }
    }

    private static function routeMap()
    {
        //https://www.spr.ru/map/solnechnogorsk-i-solnechnogorskiy-rayon/malenkiy-tokio.html
        Application::setHttpCode(200);

        if (!self::checkHtmlSuffix()) {
            return self::page404();
        }

        if (!self::$html_alias) {
            return self::page404();
        }

        $real_ids_and_aliases = self::getRealKartaAndFirmAliases(self::$route_spr[1], self::$html_alias);

        RouterDataHelper::$id_item = $real_ids_and_aliases['firm']['id'];

        if (!$real_ids_and_aliases['karta']['real_alias'] || !$real_ids_and_aliases['firm']['real_alias']) {//если хоть один из алиасов не определен - 404

            return self::page404();

        } elseif ($real_ids_and_aliases['karta']['changed'] || $real_ids_and_aliases['firm']['changed']) {//если изменился справочник или алиас - редиректим на новый урл

            $new_url = Application::$base_domain . 'map/' . $real_ids_and_aliases['karta']['real_alias'] . '/' . $real_ids_and_aliases['firm']['real_alias'] . '.html';

            Application::redirect($new_url, 301);

        } elseif (RouterDataHelper::$id_item) {

            if (RouterDataHelper::checkDomain()) {

                self::$route = [
                    'map',
                ];

                $id_firm = RouterDataHelper::$id_item;
                $_REQUEST['id_firm'] = $id_firm;

                RouterDataHelper::setApplicationDataFromFirm($id_firm);
                ApplicationData::addData('page_type', 1);

            } else {
                Application::redirect(RouterDataHelper::$firm_map_url, 301);
            }

        } else {

            //проверим нет ли урла в удаленных
            RouterDataHelper::getFirmURLsByDeletedURL();

            if (RouterDataHelper::$firm_url) {
                Application::redirect(RouterDataHelper::$firm_map_url, 301);
            } else {
                return self::page404();
            }
        }

        return true;
    }

    private static function routeOpinions()
    {
        //https://www.spr.ru/otzyvy/malenkiy-tokio.html
        if (self::$html_alias) {

            RouterDataHelper::getFirmId();

            if (RouterDataHelper::$id_item) {

                if (RouterDataHelper::checkDomain()) {

                    self::$route = [
                        'opinions',
                    ];

                    $_REQUEST['id_firm'] = RouterDataHelper::$id_item;

                } else {

                    Application::redirect(RouterDataHelper::$firm_opinions_url, 301);
                }

            } else {

                //проверим нет ли урла в удаленных
                RouterDataHelper::getFirmURLsByDeletedURL();

                if (RouterDataHelper::$firm_url) {

                    Application::redirect(RouterDataHelper::$firm_opinions_url, 301);

                } else {

                    self::page404();
                }
            }
        } elseif (!empty(self::$route_spr[1])) {

            RouterDataHelper::getNetId();

            if (RouterDataHelper::$id_item) {

                self::$route = [
                    'opinions',
                ];

                $_REQUEST['id_net'] = RouterDataHelper::$id_item;

            } else {

                self::page404();

            }

        } else {

            self::page404();
        }
    }

    /**
     * Route for division
     * Method handles url such as
     * map/mcdonalds
     * map/mcdonalds/krasnogorsk-2334.html
     * Another part of division pass another method routeDivisionDefault()
     * division/?id_net=1641
     * division/mcdonalds
     */
    private static function routeDivision()
    {
        $segments_count = count(self::$route_spr);

        if ($segments_count > 3) {
            return self::page404();
        }

        $id_net = $_GET['id_net'] ?? 0;

        if ($id_net < 1) {

            $net_alias = self::$route_spr[1] ?? false;

            if ($net_alias) {
                RouterDataHelper::getNetIDByURLAndOkrug($net_alias);
                $id_net = RouterDataHelper::$id_item;

                // check from deleted
                if ($id_net < 1) {
                    $del_net = RouterDataHelper::getNetIDByURLAndOkrugFromDel($net_alias);
                    if (!empty($del_net)) {
                        $new_alias = $del_net['url'];
                        $url = RouterDataHelper::getCurrentUrl();
                        $new_url = strtolower($url);
                        $new_url = str_replace($net_alias, $new_alias, $new_url);

                        if ($url !== $new_url) {
                            Application::redirect($new_url, 301);
                        }
                    }
                }
            }
        }

        $id_city = self::$html_alias ?? self::$route_spr[2] ?? '';

        if ($id_net) {

            //TODO для сетей нужен другой checkDomain

            if (!$id_city) {

                // only net
                self::$route = [
                    'division',
                ];

                $_REQUEST['id_net'] = $id_net;

            } else {
                // net with city
                if (RouterDataHelper::isIdCityExist($id_net, $id_city)) {
                    self::$route = [
                        'division',
                    ];

                    $_REQUEST['id_net'] = $id_net;
                    $_REQUEST['id_city'] = $id_city;
                } else {
                    $city_del = RouterDataHelper::getCityFromDel($id_net, $id_city);
                    if (!empty($city_del)) {
                        $new_alias = $city_del['url'];
                        $url = RouterDataHelper::getCurrentUrl();
                        $new_url = strtolower($url);
                        $new_url = str_replace($id_city, $new_alias, $new_url);

                        if ($url !== $new_url) {
                            Application::redirect($new_url, 301);
                        }
                    }else{
                        return self::page404();
                    }
                }
            }
        } else {
            return self::page404();
        }

        return true;
    }

    private static function routePrices()
    {
        //https://www.spr.ru/prices/arenda-pomescheniy/arenda-pomescheniya-klassa-b/

        if (!self::$html_alias) {

            self::page404();

        } else {

            RouterDataHelper::getFirmId();

            if (RouterDataHelper::$id_item) {

                if (RouterDataHelper::checkDomain()) {

                    self::$route = [
                        'prices',
                    ];

                    $_REQUEST['id_firm'] = RouterDataHelper::$id_item;

                } else {
                    Application::redirect(RouterDataHelper::$firm_price_url, 301);
                }

            } else {

                //проверим нет ли урла в удаленных
                RouterDataHelper::getFirmURLsByDeletedURL();

                if (RouterDataHelper::$firm_url) {

                    Application::redirect(RouterDataHelper::$firm_price_url, 301);

                } else {

                    self::page404();
                }
            }
        }
    }

    private static function routePricesNet($net_alias)
    {
        # /net/tanuki/prices
        RouterDataHelper::getNetIDByURLAndOkrug($net_alias);

        $id_net = RouterDataHelper::$id_item;

        if ($id_net) {
            self::$route = [
                'prices',
            ];

            $_REQUEST['id_net'] = $id_net;
            return true;
        }
        return self::page404();
    }

    private static function routeDiscountsNet($net_alias)
    {
        # /net/beeline/discounts
        RouterDataHelper::getNetIDByURLAndOkrug($net_alias);

        $id_net = RouterDataHelper::$id_item;

        if ($id_net) {
            self::$route = [
                'discounts',
            ];

            $_REQUEST['id_net'] = $id_net;
            return true;
        }
        return self::page404();
    }

    private static function getRealKartaAndFirmAliases($current_karta_alias, $current_firm_alias)
    {
        $karta_alias_changed = false;
        $firm_alias_changed = false;

        //проверим не старый ли алиас указан в урле, если старый, то возьмем новый
        $firm_id_and_real_alias = RouterDataHelper::getIdAndFirmRealAlias($current_firm_alias);

        if ($firm_id_and_real_alias['alias'] != self::$html_alias) {

            $firm_alias_changed = true;
        }

        //проверим не старый ли справочник указан в урле, если старый, то возьмем новый
        $karta_id_and_real_alias = RouterDataHelper::getIdAndRealKartaAlias($current_karta_alias, $firm_id_and_real_alias['id']);

        if ($karta_id_and_real_alias['alias'] != $current_karta_alias) {

            $karta_alias_changed = true;
        }

        return [
            'karta' => [
                'real_alias'    => $karta_id_and_real_alias['alias'],
                'id'            => $karta_id_and_real_alias['id'],
                'changed'       => $karta_alias_changed
            ],
            'firm'  => [
                'real_alias'    => $firm_id_and_real_alias['alias'],
                'id'            => $firm_id_and_real_alias['id'],
                'changed'       => $firm_alias_changed
            ],
        ];
    }

    private static function routeFirm()
    {
        //https://www.spr.ru/solnechnogorsk-i-solnechnogorskiy-rayon/malenkiy-tokio.html
        if (!self::checkHtmlSuffix()) {
            return self::page404();
        }

        if (self::$route_spr[0] == 'view.php') {

            $firm_url = RouterDataHelper::getFirmURLById((int)$_REQUEST['id_firm']);

            if ($firm_url == '') {

                self::page404();

            } else {

                Application::redirect($firm_url, 301);
            }

        } elseif (!self::$html_alias) {

            self::page404();

        } else {

            $real_ids_and_aliases = self::getRealKartaAndFirmAliases(self::$route_spr[0], self::$html_alias);

            if (!$real_ids_and_aliases['karta']['real_alias'] || !$real_ids_and_aliases['firm']['real_alias']) {//если хоть один из алиасов не определен - 404

                self::page404();

            } elseif ($real_ids_and_aliases['karta']['changed'] || $real_ids_and_aliases['firm']['changed']) {//если изменился справочник или алиас - редиректим на новый урл

                $new_url = Application::$base_domain . $real_ids_and_aliases['karta']['real_alias'] . '/' . $real_ids_and_aliases['firm']['real_alias'] . '.html';

                Application::redirect($new_url, 301);

            } else {

                RouterDataHelper::$id_item = $real_ids_and_aliases['firm']['id'];

                if (RouterDataHelper::$id_item) {

                    //проверим соответствие домена
                    if (RouterDataHelper::checkDomain()) {

                        self::$route = [
                            'firms',
                        ];

                        $_REQUEST['id_firm'] = RouterDataHelper::$id_item;

                    } else {

                        Application::redirect(RouterDataHelper::$firm_url, 301);
                    }

                } else {
                    self::page404();
                }
            }
        }
    }

    private static function checkHtmlSuffix()
    {
        $suffix = '.html';
        $request_uri_length = strlen(self::$request_uri);
        $diff = $request_uri_length - (strlen($suffix) + self::$html_index);

        return $diff === 0;
    }

    private static function routeNewURL()
    {
        //https://www.spr.ru/prices?id_firm...
        self::$route = self::$route_spr;
    }

    private static function page404()
    {
        Application::setHttpCode(410);
        Application::$router_send_abort = true;
        self::$route = ['page404'];
        return false;
    }

    private static function getTypeFromString($string)
    {
        return gettype(self::getCorrectVariable($string));
    }

    private static function getCorrectVariable($string)
    {
        $string = trim($string);
        if (empty($string) && $string !== '0') return '';
        if (!preg_match("/[^0-9.]+/", $string)) {
            if (preg_match("/[.]+/", $string)) {
                return (double)$string;
            } else {
                return (int)$string;
            }
        }

        if ($string === 'true') return true;
        if ($string === 'false') return false;
        return (string)$string;
    }

    /**
     * @param array $parts
     * @return string
     */
    private static function build_url(array $parts)
    {
        $scheme = isset($parts['scheme']) ? ($parts['scheme'] . '://') : '//';

        $host = ($parts['host'] ?? '');
        $port = isset($parts['port']) ? (':' . $parts['port']) : '';

        $user = ($parts['user'] ?? '');

        $pass = isset($parts['pass']) ? (':' . $parts['pass']) : '';
        $pass = ($user || $pass) ? "$pass@" : '';

        $path = ($parts['path'] ?? '');
        $query = isset($parts['query']) ? ('?' . $parts['query']) : '';
        $fragment = isset($parts['fragment']) ? ('#' . $parts['fragment']) : '';

        return implode('', [$scheme, $user, $pass, $host, $port, $path, $query, $fragment]);
    }
}