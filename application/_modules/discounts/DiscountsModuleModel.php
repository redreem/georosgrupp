<?php

require_once Core::$config['application_root'] . '_modules' . DIRECTORY_SEPARATOR . 'discounts' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'DiscountsModuleSQLHelper.php';

class DiscountsModuleModel extends AbstractModel
{
    use Application\Traits\ModelTrait;

    const LIMIT = 10;

    const TYPE = [
        'DISCOUNT' => 1,
        'COUPON' => 2,
        'OTHER' => 3,
    ];

    public $id_firm = 0;
    public $firm_data = [];
    public $discounts_data = [];

    public $id_discount = 0;
    public $discount_data = [];

    public $show_all_url = '';

    public $db_cache_id = 'modules/discount';

    public $id_net;
    public $net_data = [];

    /**
     * @throws \Core\Errors\HttpNotFoundException
     */
    protected function dataProcess()
    {
        $this->id_firm = (int)($_REQUEST['id_firm'] ?? 0);
        $this->id_net = (int)($_REQUEST['id_net'] ?? 0);

        if ($this->id_firm < 1 && $this->id_net < 1) {
            Application::abort(410);
        }

        $discounts_data = [];

        if ($this->id_firm > 0) {
            $this->firm_data = $this->fetchFirmById($this->id_firm);
            $discounts_data = $this->getDataByFirm($this->firm_data);
            $this->show_all_url = '/discount/' . $this->firm_data['karta_url'] . '/';
        } elseif ($this->id_net > 0) {
            $this->net_data = $this->fetchNetById($this->id_net);
            $discounts_data = $this->getDataByNet();
        }

        $this->discounts_data = $discounts_data;
    }

    public function getDataByFirm($firm_data)
    {
        $discounts_data = [];

        $result = Application::$db->query(
            DiscountsModuleSQLHelper::sel_discounts_module_for_firm(),
            [
                ':id_raion' => (int)$firm_data['ID_KARTA'],
                ':id_rubr3' => (int)$firm_data['ID_RUBR'],
                ':id_okrug' => (int)$firm_data['ID_OKRUG_FIRMA'],
                ':id_firms' => $this->id_firm,
                ':limit' => self::LIMIT
            ],
            Application::$db->SEC_PER_2_HOUR,
            $this->db_cache_id
        );

        $rows = $result->numRows();

        if ($rows < 1) {
            $result = $this->getDataByRaion($firm_data['ID_KARTA'], [$this->id_firm]);
        }

        $rows = $result->numRows();

        if ($rows < 1) {
            $result = $this->getDataByOkrug($firm_data['ID_OKRUG_FIRMA'], [$this->id_firm]);
        }

        if (!empty($result)) {
            while ($data = $result->fetchAssocArray()) {
                $discounts_data[] = DiscountsModel::fixDiscountData($data);
            }
        }
        return $discounts_data;
    }

    public function getDataByNet()
    {
        $discounts_data = [];

        $id_firms = [];
        //get firms id by id_net
        $q = Application::$db->query(
            DiscountsModuleSQLHelper::sel_id_firms_by_id_net(),
            [
                ':id_net' => $this->id_net
            ],
            Application::$db->SEC_PER_DAY,
            $this->db_cache_id
        );

        while ($row = $q->fetchAssocArray()) {
            $id_firms[] = $row['id_firm'];
        }

        $id_firms = array_filter($id_firms);
        $id_firms_text = implode(',', $id_firms);

        $result = Application::$db->query(
            DiscountsModuleSQLHelper::sel_discounts_module_for_net(),
            [
                ':id_rubr' => $this->net_data['id_rubr'],
                ':id_raion_parent' => $this->net_data['id_karta_parent'],
                ':id_net' => $this->id_net,
                ':id_okrug' => $this->net_data['id_okrug'],
                ':id_firms' => $id_firms_text,
                ':limit' => self::LIMIT
            ],
            Application::$db->SEC_PER_5_HOUR,
            $this->db_cache_id
        );

        $rows = $result->numRows();

        if ($rows < 1) {
            $result = $this->getDataByOkrug($this->net_data['id_okrug'], $id_firms, $this->id_net);
        }

        if (!empty($result)) {
            while ($data = $result->fetchAssocArray()) {
                $discounts_data[] = DiscountsModel::fixDiscountData($data);
            }
        }
        return $discounts_data;
    }

    private function getDataByOkrug($id_okrug, $id_firms = [], $id_net = 0)
    {
        $id_firms = array_filter($id_firms);
        $id_firms_text = implode(',', $id_firms);
        $result = Application::$db->query(
            DiscountsModuleSQLHelper::sel_by_okrug(),
            [
                ':id_okrug' => $id_okrug,
                ':id_firms' => $id_firms_text,
                ':id_net' => $id_net,
                ':limit' => self::LIMIT
            ],
            Application::$db->SEC_PER_2_HOUR,
            $this->db_cache_id
        );

        return $result;
    }

    private function getDataByRaion($id_raion, $id_firms = [])
    {
        $id_firms = array_filter($id_firms);
        $id_firms_text = implode(',', $id_firms);
        $result = Application::$db->query(
            DiscountsModuleSQLHelper::sel_by_raion(),
            [
                ':id_raion' => $id_raion,
                ':id_firms' => $id_firms_text,
                ':limit' => self::LIMIT
            ],
            Application::$db->SEC_PER_2_HOUR,
            $this->db_cache_id . '0'
        );

        //var_dump(Application::$db->last_query);

        return $result;
    }

}
