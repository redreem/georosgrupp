<?php

class CommonSEOHelper
{
    public static $description;
    public static $title;
    public static $keywords;
    public static $robots;

    public static function setSEOData()
    {
        switch (Application::$application_name) {

            case 'Business':

                switch (Application::$action) {
                    case 'monopolist':
                        self::$title = 'Пакет услуг "Монополист": оптимальная модель b2c маркетинга для вашей компании';
                        self::$description = 'Продвигайте вашу компанию на рынке при помощи b2c маркетинга с пакетом услуг "Монополист"!';
                        self::$keywords = 'Пакет услуг "Монополист": оптимальная модель b2c маркетинга для вашей компании';
                        break;
                    case 'reputation':
                        self::$title = 'Пакет услуг "Идеальная репутация": управление репутацией вашей компании в интернете, формирование отзывов';
                        self::$description = 'Управляйте деловой репутацией вашей компнии и отзывами о ней в интернете с пакетом услуг "Идеаольная репутация"!';
                        self::$keywords = 'Пакет услуг "Идеальная репутация": управление репутацией вашей компании в интернете, формирование отзывов';
                        break;
                    default:
                        self::$title = 'Реклама на сайте СПР: условия размещения, тарифы и контактные данные';
                        self::$description = 'Закажите рекламу на сайте СПР и о вас знают миллионы людей!';
                        self::$keywords = 'Реклама на сайте СПР: условия размещения, тарифы и контактные данные';
                        break;
                }
                break;

            case 'Discounts':
                /** @var DiscountsModel $model */
                $model = Application::$model;

                if ($model->id_firm > 0) {
                    self::$title = 'Скидки и акции | ' . $model->firm_data['name'] . ' | ' . $model->firm_data['address'];
                    self::$description = 'Все акции и скидки организации ' .
                        $model->firm_data['FIRMNAME_ONLY'] .
                        ' (' . $model->firm_data['FIRMA_OPISANIE'] . ') ' .
                        ' по адресу: ' . $model->firm_data['address'];
                    self::$keywords = 'Скидки и акции, ' . $model->firm_data['name'] . ', ' . $model->firm_data['address'];
                }

                if ($model->id_net > 0) {
                    self::$title = 'Скидки и акции сети ' . $model->net_data['name_net'];
                    self::$description = 'Скидки и акции сети ' . $model->net_data['name_net'];
                    self::$keywords = 'Скидки и акции ' . $model->net_data['name_net'];
                }
                break;

            case 'Statistics':

                self::$description = 'статистика посещаемости ' .
                    ((Application::$model->is_net && Application::$model->id_firm != 0) ? (' сети ' . Application::$model->net_data['name']) : Application::$model->firm_sql_data['FIRMNAME_ONLY']) .
                    ', рубрики ' . Application::$model->firm_sql_data['RUBR_NAME'] . ' в ' .
                    Application::$model->firm_sql_data['SPRAV_NAME_P'] . ' и ' . Application::$model->firm_sql_data['OKRUG_NAME_P'];

                if (empty(Application::$model->id_firm)) {
                    self::$title = 'Статистика';
                } else {
                    self::$title = 'Статистика ' . Application::$model->firm_sql_data['FIRMNAME_ONLY'] . ' по адресу ' . Application::$model->firm_sql_data['ADRES_FIRM'];
                }

                self::$keywords = Application::$model->firm_sql_data['FIRMNAME'] .
                    ', ' . Application::$model->firm_sql_data['RUBR_NAME'] .
                    ', ' . Application::$model->firm_sql_data['SPRAV_NAME'] .
                    ', ' . Application::$model->firm_sql_data['NAME_OKRUG_FIRMA'];

                self::$robots = 'noindex, nofollow';
                break;

            case 'Map':
                /** @var MapModel $model */
                $model = Application::$model;
                self::$title = 'Схема проезда | ' . $model->firm_data['name'] . ' | ' . $model->firm_data['address'] . ' | Как проехать';
                self::$description = 'Как проехать до организации ' . $model->firm_data['name'] . ' по адресу: ' . $model->firm_data['address'] . ', подробная схема проезда';
                self::$keywords = 'Схема проезда, ' . $model->firm_data['name'] . ', ' . $model->firm_data['address'] . ', как проехать';
                break;

            case 'Prices':
                /** @var PricesModel $model */
                $model = Application::$model;
                if ($model->id_firm > 0) {
                    self::$title = 'Цены на товары и услуги |  ' . $model->firm_data['name'] . ' по адресу: ' . $model->firm_data['address'];
                    self::$description = 'Актуальные цены на услуги и товары организации  ' .
                        $model->firm_data['FIRMNAME_ONLY'] .
                        ' (' . $model->firm_data['FIRMA_OPISANIE'] . ') ' .
                        ' по адресу: ' . $model->firm_data['address'];
                    self::$keywords = 'Цены на товары и услуги, ' . $model->firm_data['name'] . ', ' . $model->firm_data['address'];
                } else {
                    self::$title = 'Цены на товары и услуги сети ' . $model->net_data['name_net'];
                    self::$description = 'Актуальные цены на услуги и товары сети № ' . $model->id_net;
                    self::$keywords = 'Цены на товары и услуги № ' . $model->id_net;
                }
                break;

            case 'Opinions':
                $firm_data = Application::$model->firm_data;

                if (!empty(Application::$model->firm_data['zag_otzyvy'])) {
                    self::$title = Application::$model->firm_data['zag_otzyvy'] . ' | ' . Application::$model->firm_data['opinions_count'] . ' отзывов';
                    self::$description = Application::$model->firm_data['zag_otzyvy'] . ' : ' .
                        'положительных отзывов; ' . Application::$model->firm_data['opinions_pros_count'] . ' , ' .
                        ' отрицательных; ' . Application::$model->firm_data['opinions_cons_count'];
                    self::$keywords = Application::$model->firm_data['zag_otzyvy'];
                } else {
                    self::$title = "Отзывы о " . Application::$model->firm_data['name'] . ' | ' . Application::$model->firm_data['opinions_count'] . ' отзывов';
                    self::$description = "Отзывы о " . Application::$model->firm_data['name'] . ' : ' .
                        'положительных отзывов; ' . Application::$model->firm_data['opinions_pros_count'] . ' , ' .
                        ' отрицательных; ' . Application::$model->firm_data['opinions_cons_count'];
                    self::$keywords = "Отзывы о " . Application::$model->firm_data['name'];
                }

                if (key_exists('new_title_opinion', $firm_data) && !empty($firm_data['new_title_opinion'])) {
                    self::$title = $firm_data['new_title_opinion'];
                }

                break;

            case 'Stories':

                self::$title = 'Видео и фото ' . Application::$model->firm_data['name'] . ' | ' . Application::$model->firm_data['address'];
                self::$description = 'Фотографии и видеоматериалы организации  ' .
                    Application::$model->firm_data['FIRMNAME_ONLY'] .
                    ' (' . Application::$model->firm_data['FIRMA_OPISANIE'] . ') ' .
                    ' по адресу: ' . Application::$model->firm_data['address'];
                self::$keywords = 'Скидки и акции, ' . Application::$model->firm_data['name'] . ', ' . Application::$model->firm_data['address'];
                break;

            case 'Firms':

                if (Application::$model->metro) {

                    self::$title = Application::$model->firm_data['name'] . ' | адрес, телефон, официальный сайт, часы работы  | ' . Application::$model->firm_data['address']
                        . ' | метро ' . Application::$model->first_metro;
                    self::$description = 'Контактные данные организации ' . Application::$model->firm_data['FIRMNAME_ONLY'] .
                        ' : телефон, часы работы, официальный сайт, находящейся по адресу: ' . Application::$model->firm_data['address'] .
                        ' у метро: ' . Application::$model->first_metro;
                    self::$keywords = Application::$model->firm_data['name'] . ' адрес, телефон, официальный сайт, часы работы ';

                } elseif (!in_array(Application::$action, ['addFirm', 'addDivisions'])) {

                    self::$title = Application::$model->firm_data['name'] . ' | адрес, телефон, официальный сайт, часы работы  | ' . Application::$model->firm_data['address'];
                    self::$description = 'Контактные данные организации ' . Application::$model->firm_data['FIRMNAME_ONLY'] .
                        ' : телефон, часы работы, официальный сайт, находящейся по адресу: ' . Application::$model->firm_data['address'];
                    self::$keywords = Application::$model->firm_data['name'] . ' адрес, телефон, официальный сайт, часы работы ';
                } else {

                    self::$title = '';
                    self::$description = '';
                    self::$keywords = '';
                }

                break;

            case 'Division':

                self::$title = Application::$model->title_page . ' на карте | ' . Application::$model->firm_count . ' адресов';
                self::$description = Application::$model->title_page . ': на карте отмечены все ' . Application::$model->firm_count . ' адресов';
                self::$keywords = Application::$model->title_page . ' на карте';
                break;

            default:
            case 'default':

                self::$description = 'СПР';
                self::$title = 'СПР';
                self::$keywords = 'СПР';
                break;
        }
    }
}
