<?php

require_once Core::$config['application_root'] . 'common' . DIRECTORY_SEPARATOR . 'CommonModel.php';
require_once Core::$config['application_root'] . 'page404' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'Page404SQLHelper.php';

class Page404Model extends AbstractModel
{
    public $url;
    public $breadcrumbs = [];
    public $opinions_total_number = '';
    public $opinions_total_number_text = '';
    public $firms_total_number = '';
    public $firms_total_number_text = '';
    public $district_info = null;
    private $common_model;
    public $image_404 = '';

    public $db_cache_id = '404';

    public $ads_block = '';

    public function __construct()
    {
        $this->common_model = new CommonModel;

        $ads_block_module = new Modules('ads_block',
            [
                'id_raion' => 700055,
                'id_okrug' => 0,
                'tip_pages' => 2,
                'id_rubrik' => 385,
                'id_rparent' => 0,
                'id_rparent2' => 0,
                'limit' => 1
            ]
        );

        $this->ads_block = $ads_block_module->content;

        parent::__construct();
    }

    protected function dataProcess()
    {
        $this->breadcrumbs = [];

        if (Application::$router_send_abort) {
            $this->image_404 = '/prod_src/img/page404_router.jpg';
        } else {
            $this->image_404 = '/prod_src/img/page404.jpg';
        }

        $this->url = Application::$base_domain_short . $_SERVER['REQUEST_URI'];

        $this->district_info = $this->common_model::getDistrict();
        $id_okrug = (int)$this->district_info['id_okrug'];

        $opinions_total = (int)Application::$db->query(
            Page404SQLHelper::sql_get_opinions_count(),
            [
                ':id_okrug' => $id_okrug
            ],
            Application::$db->SEC_PER_DAY,
            $this->db_cache_id
        )->fetchAssocArray()['total'];

        $this->opinions_total_number = number_format($opinions_total, 0, '.', ' ');
        $this->opinions_total_number_text = $this->common_model::wordWithNumber(
            $opinions_total,
            [
                'отзыв', 'отзыва', 'отзывов'
            ]
        );

        $firms_total = (int)Application::$db->query(
            Page404SQLHelper::sql_get_firms_count(),
            [
                ':id_okrug' => $id_okrug
            ],
            Application::$db->SEC_PER_DAY,
            $this->db_cache_id
        )->fetchAssocArray()['total'];

        // get nearest 1000
        $firms_total_thousands = $firms_total;
        if ($firms_total_thousands < 1000) {
            $firms_total_thousands = 1000;
        } else {
            $firms_total_thousands = floor($firms_total_thousands / 1000) * 1000;
        }

        $this->firms_total_number = number_format($firms_total_thousands, 0, '.', ' ');
    }
}