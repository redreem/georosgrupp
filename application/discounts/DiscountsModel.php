<?php

use Application\Services\Link;

require_once Core::$config['application_root'] . 'discounts' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'DiscountsSQLHelper.php';
require_once Core::$config['application_root'] . 'discounts' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'DiscountsDataHelper.php';

class DiscountsModel extends AbstractModel
{
    use Application\Traits\ModelTrait;

    const TYPE = [
        'DISCOUNT' => 1,
        'COUPON' => 2,
        'OTHER' => 3,
    ];

    const TEXT = [
        'BUTTON_TEXT' => [
            self::TYPE['DISCOUNT'] => 'Узнать подробнее',
            self::TYPE['COUPON'] => 'Купить купон',
            self::TYPE['OTHER'] => 'Узнать подробнее',
        ],
        'TEXT_SEPARATOR' => '%%%%%%%%%%',
        'DAY_NAMES' => ['день', 'дня', 'дней'],
        'HOUR_NAMES' => ['час', 'часа', 'часов'],
        'LESS_THAN_HOUR' => 'Менее часа',
    ];

    public static $data = [];

    public $cabinet_link = '';

    public $breadcrumbs = [];
    public $id_firm = 0;
    public $firm_data = [];
    public $discounts_data = [];
    public $discounts_ids = [];

    public $id_discount = 0;
    public $discount_data = [];

    public $scroll_content = '';
    public $arrows_content = '';
    public $views = "просмотров";
    public $detail = "Подробнее";
    public $today_end = "Акция сегодня заканчивается!";
    public $leave_end = "До завершения осталось";
    public $already_end = "Акция закончилась";

    public $id_net = 0;
    public $net_data = [];
    public $first_filials = [];
    public $filials_data = [];

    public static $allowed_urls = [];

    public $ads_block = '';

    public $db_cache_id = 'discounts';

    public $link_service;
    public $popup_url_template = '';

    public $limit_count = 10;

    public function __construct()
    {
        $this->link_service = new Link();

        parent::__construct();
    }

    /**
     * @throws \Core\Errors\HttpNotFoundException
     */
    protected function dataProcess()
    {
        $this->id_firm = (int)($_REQUEST['id_firm'] ?? 0);
        $this->id_net = (int)($_REQUEST['id_net'] ?? 0);
        $this->id_discount = (int)($_REQUEST['id_discount'] ?? 0);

        self::$allowed_urls = ['georostgrupp.ru'];

        $this->popup_url_template = $this->link_service->getPopupDiscountUrlTemplate();

        Module::load('image');

        switch (Application::$action) {

            default:
            case 'default':
                $this->getDiscountData();
                $this->setFEData();
                $this->setBreadCrumbs();

                $ads_block_module = new Modules('ads_block',
                    [
                        'id_raion' => ApplicationData::getData('id_raion'),
                        'id_okrug' => ApplicationData::getData('id_okrug'),
                        'tip_pages' => 2,
                        'id_rubrik' => ApplicationData::getData('id_rubr'),
                        'id_rparent' => ApplicationData::getData('id_rubr_parent'),
                        'id_rparent2' => ApplicationData::getData('id_rubr_2'),
                        'limit' => 1
                    ]
                );

                $this->ads_block = $ads_block_module->content;
                break;

            case 'getdiscount':
                $this->getDiscountData();
                break;

            case 'sendClickLog':
                // Обработка клика по ссылке скидки

                if (isset($_REQUEST['click'])) {

                    if (
                        !isset($_SERVER['HTTP_USER_AGENT']) || !$_SERVER['HTTP_USER_AGENT']
                        ||
                        !isset($_SERVER['REMOTE_ADDR']) || !$_SERVER['REMOTE_ADDR']
                    ) {
                        Application::abort(410);
                    }

                    $this->getDiscountData();

                    $session = md5($_SERVER['HTTP_USER_AGENT']);
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $query = Application::$db->query(
                        DiscountsSQLHelper::sel_log_click(),
                        [':id_discount' => $this->id_discount, ':ip' => $ip, ':session' => $session]
                    );

                    if (!$query->fetchAssocArray()) {

                        Application::$db->query(DiscountsSQLHelper::ins_log_click(),
                            [
                                ':id_discount' => $this->id_discount,
                                ':ip' => $ip, ':session' => $session,
                                ':id_firm' => $this->discount_data['id_firm']
                            ]
                        );
                        Application::$db->query(DiscountsSQLHelper::updCountClick(),
                            [
                                ':id_discount' => $this->id_discount
                            ]
                        );

                    }
                }
                break;

            case 'ShowNews':
                break;

            case 'UpdateSocial':
                break;

            case 'scrolling':

                // Подгрузка скидок для простой фирмы
                if (isset($_REQUEST['num']) and isset($_REQUEST['id_firm'])) {

                    $num = (int)$_REQUEST['num'];
                    Application::$db->debug_mode = true;
                    $query = Application::$db->query(
                        DiscountsSQLHelper::sel_firm_discounts_limit(),
                        [
                            ':id_firm' => (int)$_REQUEST['id_firm'],
                            ':limit_count' => $this->limit_count,
                            ':offset' => $num
                        ],
                        Application::$db->SEC_PER_5_HOUR,
                        $this->db_cache_id
                    );

                    while ($data = $query->fetchAssocArray()) {

                        $this->discounts_data[] = self::fixDiscountData($data);
                    }
                }
                break;
        }
    }

    public function setFEData()
    {
        FE::addData('id_net', $this->id_net);
        FE::addData('id_firm', $this->id_firm);
        FE::addData('views', $this->views);
        FE::addData('detail', $this->detail);
        FE::addData('today_end', $this->today_end);
        FE::addData('leave_end', $this->leave_end);
        FE::addData('already_end', $this->already_end);
        FE::addData('popup_url_template', $this->popup_url_template);
        FE::addData('limit_count', $this->limit_count);
    }

    /**
     * @throws \Core\Errors\HttpNotFoundException
     */
    public function getDiscountData()
    {
        // Данные по скидке
        if ($this->id_discount) {

            $query = Application::$db->query(
                DiscountsSQLHelper::sel_discount(),
                [
                    ':id_discount' => $this->id_discount
                ],
                Application::$db->SEC_PER_5_HOUR,
                $this->db_cache_id
            );
            $this->discount_data = $query->fetchAssocArray();

            if (!$this->discount_data) {
                Application::abort(410);
            }

            if ($this->id_net > 0) {
                $this->filials_data = $this->getFilialsData($this->discount_data['id_skidki_net']);
            }

            $id_org_name = $this->id_firm > 0 ? 'id_firm' : 'id_net';
            $id_org = $this->id_firm > 0 ? $this->id_firm : $this->id_net;

            $popup_link = $this->link_service->getPopupDiscountUrl($this->id_discount, $id_org_name, $id_org);

            $this->discount_data['popup_link'] = $popup_link;
            $this->discount_data['popup_link_share'] = urlencode($popup_link);

            $this->discount_data = self::fixDiscountData($this->discount_data);
        }

        if ($this->id_firm > 0) {
            $this->firm_data = $this->fetchFirmById($this->id_firm);

            self::$data['gmt'] = $this->firm_data['gmt'];

            if (empty($this->firm_data)) {
                Application::abort(410);
            }

            $this->cabinet_link = $this->cabinetLink(['id_firm' => $this->id_firm]);

            if ($this->firm_data['discounts_count'] < 1) {
                Application::redirect($this->cabinet_link);
            }

            $query = Application::$db->query(
                DiscountsSQLHelper::sel_firm_discounts(),
                [
                    ':id_firm' => $this->firm_data['id'],
                    ':limit_count' => $this->limit_count,
                ],
                Application::$db->SEC_PER_5_HOUR,
                $this->db_cache_id
            );

            while ($data = $query->fetchAssocArray()) {
                $this->discounts_ids[] = $data['id'];
                $this->discounts_data[] = self::fixDiscountData($data);
            }
        }

        // net discounts
        if ($this->id_net > 0) {
            $this->net_data = $this->fetchNetById($this->id_net);

            if (empty($this->net_data)) {
                Application::abort(410);
            }

            $query = Application::$db->query(
                DiscountsSQLHelper::sel_net_discounts(),
                [
                    ':id_net' => $this->id_net,
                    ':limit_count' => $this->limit_count
                ],
                Application::$db->SEC_PER_5_HOUR,
                $this->db_cache_id
            );

            while ($data = $query->fetchAssocArray()) {
                $this->discounts_ids[] = $data['id'];
                $this->discounts_data[] = self::fixDiscountData($data);
            }

            $ids_skidki_net = array_column($this->discounts_data, 'id_skidki_net');
            $this->first_filials = $this->getFirstFilialsData($ids_skidki_net);

            foreach ($this->discounts_data as $k => $discount) {
                $top_filial = [];
                if (array_key_exists($discount['id_skidki_net'], $this->first_filials)) {
                    $top_filial = $this->first_filials[$discount['id_skidki_net']];
                }
                $this->discounts_data[$k]['top_filial'] = $top_filial;
            }
        }

        $this->setPrevNextId();
    }

    public static function fixDiscountData($data)
    {
        $preview_length = 200;
        $ret = $data;
        $ret['id'] = (int)$data['id'];

        $ret['image_url'] = sprintf('//www.spr.ru/sale_img/%s/%d/%d.%s',
            strftime('%Y-%m', strtotime($data['date_add'])), $data['id_firm'], $data['id'], $data['img_extension']);

        $file_url = sprintf(Application::$main_domain . 'sale_img/%s/%d/', strftime('%Y-%m', strtotime($data['date_add'])), $data['id_firm']);
        $file_name = $data['id'] . '.' . $data['img_extension'];


        $base_domain = trim(Application::$base_domain_short, 'www.');

        $crop_params = [
            'file_url' => $file_url,
            'file_name' => $file_name,
            'date_moder' => $data['date_moder'],
            'cache_id' => false,
            'width' => 483,
            'height' => false,
            'source_type' => ImageModule::SOURCE_TYPE_IS_URL
        ];

        $parse_url = parse_url($file_url . $file_name);
        $path = $parse_url['path'];
        $ret['image_relative_url'] = ltrim($path, '/');

        // local path for images - prod
        if (in_array($base_domain, self::$allowed_urls)) {
            $parse_url = parse_url($file_url);
            $path = $parse_url['path'];

            $crop_params['file_url'] = ltrim($path, '/');
            $crop_params['source_type'] = ImageModule::SOURCE_TYPE_IS_PATH;

            $file_url = Application::$base_domain . $ret['image_relative_url'];
            $ret['image_url'] = $file_url;
        }

        //echo '<span class="qweqweqwe" style="display:none"> ' . print_r($crop_params, true) . '</span>';

        $ret['image_resizes_url'] = ImageModule::create_crop_image(
            $crop_params['file_url'],
            $crop_params['file_name'],
            $crop_params['date_moder'],
            $crop_params['cache_id'],
            $crop_params['width'],
            $crop_params['height'],
            $crop_params['source_type']
        );

        $ret['heading'] = htmlspecialchars($data['zag_skidki'], ENT_COMPAT, 'CP1251');

        $data['tip_skidki'] = intval($data['tip_skidki']);
        $ret['tip_skidki'] = $data['tip_skidki'];

        $ret['percent'] = '';
        if ($data['procent'] > 0 && $data['procent'] < 101) {
            $ret['percent'] = '-' . (int)$data['procent'] . '%';
        }

        $ret['percent_original'] = $data['procent'];

        // Текст разделен на 2 части магической последовательностью символов
        $ret['preview'] = explode(self::TEXT['TEXT_SEPARATOR'], $data['text_skidki'])[0];
        $ret['preview'] = strip_tags($ret['preview']);
        $ret['preview'] = html_entity_decode($ret['preview'], ENT_COMPAT, 'CP1251');
        $ret['preview'] = htmlspecialchars($ret['preview'], ENT_COMPAT, 'CP1251');

        $ret['preview_small'] = DiscountsDataHelper::trimString($ret['preview'], $preview_length);

        $ret['full'] = $ret['preview'];

        $ret['button_text'] = self::TEXT['BUTTON_TEXT'][$ret['tip_skidki']];
        $ret['discount_url'] = '';

        $need_round = !($data['view_cent'] ?? 0);

        $price = ($data['tip_skidki'] === self::TYPE['COUPON']) ? $data['gold_skidka2'] : $data['gold_skidka'];
        if ($price) {
            $ret['price'] = DiscountsDataHelper::formatNumber($price, $need_round, $data['sokr_gold']);
        } else {
            $ret['price'] = '';
        }

        if ($data['gold'] && $price != $data['gold']) {
            $price_orig = $data['gold'];
            $ret['price_orig'] = DiscountsDataHelper::formatNumber($price_orig, $need_round, $data['sokr_gold']);
        } else {
            $ret['price_orig'] = '';
        }

        if (isset($data['date_end'])) {
            $gmt = self::$data['gmt'] ?? null;

            if (!empty($gmt)) {
                $current_time = DiscountsDataHelper::getCurrentTimeWithGMT($gmt);
                $time_zone = $current_time->getTimezone();
                $now = $current_time;

                $end = new DateTime($data['date_end']);
                $end->setTimezone($time_zone);
            } else {
                $end = new DateTime($data['date_end']);
                $now = new DateTime('now');
            }

            $is_ended = $end < $now;
            $ret['active'] = !$is_ended;

            if (!$is_ended) {

                $interval = $now->diff($end);
                $days = (int)$interval->format('%a');
                $hours = (int)$interval->format('%H');

                $days_text = $days . ' ' . DiscountsDataHelper::wordWithNumber($days, self::TEXT['DAY_NAMES']);
                $hours_text = $hours . ' ' . DiscountsDataHelper::wordWithNumber($hours, self::TEXT['HOUR_NAMES']);

                if ($hours < 1) {
                    $hours_text = '';
                } else if ($days === 0) {
                    $hours_text = self::TEXT['LESS_THAN_HOUR'];
                }

                if ($days < 1) {
                    $days_text = '';
                }

                $ret['before_end'] = "{$days_text} {$hours_text}";

                if ($days < 1) {
                    $ret['today_end'] = 1;
                }
            } else {
                $ret['before_end'] = 0;
            }
        }
        if (isset($data['url_count_click'])) {
            $ret['views'] = (int)$data['url_count_click'];
        }

        if ($data['tip_skidki'] === self::TYPE['DISCOUNT'] || $data['tip_skidki'] === self::TYPE['COUPON']) {

            $ret['gold'] = DiscountsDataHelper::formatNumber($data['gold'], $need_round, $data['sokr_gold']);

            $ret['gold_skidka'] = DiscountsDataHelper::formatNumber($data['gold_skidka'], $need_round, $data['sokr_gold']);

            if ($data['tip_skidki'] === self::TYPE['COUPON']) {
                $data['gold_raznica'] = $data['gold'] - ($data['gold_skidka2'] + $data['gold_skidka']);
            } else {
                $data['gold_raznica'] = $data['gold'] - $data['gold_skidka'];
            }

            $ret['gold_raznica'] = $data['gold_raznica'];
            $ret['gold_raznica_format'] = DiscountsDataHelper::formatNumber($data['gold_raznica'], $need_round, $data['sokr_gold']);
            $ret['extra_price'] = $data['gold_skidka2'];
            $ret['extra_price_text'] = DiscountsDataHelper::formatNumber($data['gold_skidka2'], $need_round, $data['sokr_gold']);
        }

        return $ret;
    }

    /**
     * Выставляем предыдущие/следующие id
     */
    private function setPrevNextId()
    {
        if (!empty($this->discount_data)) {

            $this->discount_data['next_id'] = 0;
            $this->discount_data['prev_id'] = 0;

            foreach ($this->discounts_ids as $k => $id) {
                if ($this->discount_data['id'] == $id) {
                    if (isset($_REQUEST['module']) && $_REQUEST['module'] == 'discounts') {
                        $this->discount_data['prev_id'] = ($k > 0) ? $this->discounts_ids[$k - 1] : $this->discounts_ids[count($this->discounts_ids) - 1];
                        $this->discount_data['next_id'] = (isset($this->discounts_ids[$k + 1])) ? $this->discounts_ids[$k + 1] : $this->discounts_ids[0];
                    } else {
                        $this->discount_data['prev_id'] = ($k > 0) ? $this->discounts_ids[$k - 1] : 0;
                        $this->discount_data['next_id'] = (isset($this->discounts_ids[$k + 1])) ? $this->discounts_ids[$k + 1] : 0;
                    }
                    break;
                }
            }

            $id_org_name = $this->id_firm > 0 ? 'id_firm' : 'id_net';
            $id_org = $this->id_firm > 0 ? $this->id_firm : $this->id_net;

            if ($this->discount_data['next_id'] > 0) {
                $link = $this->link_service->getPopupDiscountUrl($this->discount_data['next_id'], $id_org_name, $id_org);
                $this->discount_data['next_link'] = $link;
            }
            if ($this->discount_data['prev_id'] > 0) {
                $link = $this->link_service->getPopupDiscountUrl($this->discount_data['prev_id'], $id_org_name, $id_org);
                $this->discount_data['prev_link'] = $link;
            }

        }
    }

    private function setBreadCrumbs()
    {
        switch (Application::$action) {

            default:
            case 'default':
                if ($this->id_firm > 0) {
                    $this->breadcrumbs = [
                        ['name' => $this->firm_data['sprav_name'], 'url' => $this->firm_data['sprav_url'], 'active' => 0],
                        ['name' => $this->firm_data['FIRMNAME_ONLY'], 'url' => $this->firm_data['firm_url'], 'active' => 0],
                        ['name' => 'Скидки и акции', 'url' => '', 'active' => 1],
                    ];
                }

                if ($this->id_net > 0) {
                    $division_link = $this->net_data['division_url'];
                    $this->breadcrumbs = [
                        ['name' => 'Сети', 'url' => Application::$base_domain . 'net/', 'active' => 0],
                        ['name' => $this->net_data['name_net'], 'url' => $division_link, 'active' => 0],
                        ['name' => 'Скидки и акции', 'url' => '', 'active' => 1],
                    ];
                }
                break;
        }
    }

    /**
     * Filials data discounts for net
     * @param $id_skidki_net
     * @return array
     */
    private function getFilialsData($id_skidki_net)
    {
        $limit_show = 5;
        $hide_class = 'hide';

        $result = [
            'has_data' => false,
            'has_more' => false,
            'data' => [],
        ];

        if ($id_skidki_net > 0 && $this->id_net > 0) {
            $data = [];

            $query = Application::$db->query(
                DiscountsSQLHelper::sel_firm_filials_discounts(),
                [
                    ':id_net' => $this->id_net,
                    ':id_skidki_net' => $id_skidki_net
                ],
                Application::$db->SEC_PER_5_HOUR,
                $this->db_cache_id
            );

            if (!empty($query)) {
                while ($row = $query->fetchAssocArray()) {
                    if (!empty($row)) {
                        $data[] = self::fixDiscountData($row);
                    }
                }
            }

            $count_of_filials = count($data);

            for ($i = 0; $i < $count_of_filials; $i++) {
                $data[$i]['css_class'] = '';
            }

            if ($count_of_filials > $limit_show) {
                for ($i = $limit_show; $i < $count_of_filials; $i++) {
                    $data[$i]['css_class'] = $hide_class;
                }
            }

            $result = [
                'has_data' => $count_of_filials > 0,
                'has_more' => $count_of_filials > $limit_show,
                'data' => $data
            ];

            if ($result['has_more']) {
                $diff = $count_of_filials - $limit_show;
                $end_diff = DiscountsDataHelper::wordWithNumber($diff,
                    ['филиал', 'филиала', 'филиалов']
                );
                $result['show_all_title'] = "Еще {$diff} {$end_diff}";
            }
        }
        return $result;
    }

    private function getFirstFilialsData(array $ids_skidki_net)
    {
        $data = [];
        $ids_skidki_net = array_filter($ids_skidki_net);
        if (count($ids_skidki_net) > 0 && $this->id_net > 0) {

            $ids_skidki_net_text = implode(',', $ids_skidki_net);

            $query = Application::$db->query(
                DiscountsSQLHelper::sel_firm_filial_first_discounts(),
                [
                    ':id_net' => $this->id_net,
                    ':ids_skidki_net' => $ids_skidki_net_text
                ],
                Application::$db->SEC_PER_5_HOUR,
                $this->db_cache_id
            );

            if (!empty($query)) {
                while ($row = $query->fetchAssocArray()) {
                    if (!empty($row)) {
                        $data[$row['id_skidki_net']] = self::fixDiscountData($row);
                    }
                }
            }
        }
        return $data;
    }
}