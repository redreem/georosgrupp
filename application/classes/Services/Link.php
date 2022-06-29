<?php

namespace Application\Services;

use Application;

class Link
{
    const LINK = [
        'CABINET' => 'georostgrupp.ru',
        'ADD_OPINION' => 'georostgrupp.ru'
    ];

    private $current_domain = '';

    public function __construct()
    {
        $this->current_domain = rtrim(Application::$base_domain, '/');
    }

    public function getDivisionUrl($net_alias, $domain = '')
    {
        $division_url_template = '/net/{net_alias}/';
        if (empty($domain)) {
            $domain = $this->current_domain;
        }
        return $domain . $this->getTemplateString($division_url_template, ['net_alias' => $net_alias]);
    }

    public function getDivisionCityUrl($net_alias, $city, $domain = '')
    {
        $division_url_template = '/net/{net_alias}/{city}/';
        if (empty($domain)) {
            $domain = $this->current_domain;
        }
        return $domain . $this->getTemplateString($division_url_template,
                [
                    'net_alias' => $net_alias,
                    'city' => $city
                ]
            );
    }

    public function getNetPricesUrl($net_alias, $domain = '')
    {
        $division_url_template = '/net/{net_alias}/prices/';
        if (empty($domain)) {
            $domain = $this->current_domain;
        }
        return $domain . $this->getTemplateString($division_url_template,
                [
                    'net_alias' => $net_alias,
                ]
            );
    }

    public function getNetOpinionsUrl($net_alias, $domain = '')
    {
        $division_url_template = '/net/{net_alias}/otzyvy/';
        if (empty($domain)) {
            $domain = $this->current_domain;
        }
        return $domain . $this->getTemplateString($division_url_template,
                [
                    'net_alias' => $net_alias,
                ]
            );
    }

    public function getNetDiscountsUrl($net_alias, $domain = '')
    {
        $division_url_template = '/net/{net_alias}/discounts/';
        if (empty($domain)) {
            $domain = $this->current_domain;
        }
        return $domain . $this->getTemplateString($division_url_template,
                [
                    'net_alias' => $net_alias,
                ]
            );
    }

    /**
     * Popup price url
     * @param $id_price
     * @param $id_org_name
     * @param $id_org
     * @param string $domain
     * @return string
     */
    public function getPopupPriceUrl($id_price, $id_org_name, $id_org, $domain = '')
    {
        $popup_url_template = '/page/prices?{id_org_name}={id_org}&id_price={id_price}';
        if (empty($domain)) {
            $domain = $this->current_domain;
        }
        return $domain . $this->getTemplateString($popup_url_template,
                [
                    'id_org_name' => $id_org_name,
                    'id_org' => $id_org,
                    'id_price' => $id_price
                ]
            );
    }

    public function getPopupOpinionAnswersUrl($id_opinion, $id_org_name, $id_org, $domain = '')
    {
        $popup_url_template = '/page/opinions?action=getOpinionAnswers&{id_org_name}={id_org}&id_opinion={id_opinion}';
        if (empty($domain)) {
            $domain = $this->current_domain;
        }
        return $domain . $this->getTemplateString($popup_url_template,
                [
                    'id_org_name' => $id_org_name,
                    'id_org' => $id_org,
                    'id_opinion' => $id_opinion
                ]
            );
    }

    public function getTemplateString(string $template, array $data)
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{' . strtolower($key) . '}', $value, $template);
        }
        return $template;
    }

    public function getCabinetLink(array $data = [])
    {
        $link = self::LINK['CABINET'];
        $keys = ['id_firm', 'id_net'];
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                $link = "{$link}?{$key}={$data[$key]}";
                break;
            }
        }
        return $link;
    }

    public function getAddOpinionLink(int $id_firm)
    {
        $link = self::LINK['ADD_OPINION'];
        $link = $this->current_domain . "{$link}&id_firm_forum={$id_firm}";
        return $link;
    }

    /**
     * Popup price url
     * @param $id_discount
     * @param $id_org_name
     * @param $id_org
     * @param string $domain
     * @return string
     */
    public function getPopupDiscountUrl($id_discount, $id_org_name, $id_org, $domain = '')
    {
        $popup_url_template = $this->getPopupDiscountUrlTemplate();
        if (empty($domain)) {
            $domain = $this->current_domain;
        }
        return $domain . $this->getTemplateString($popup_url_template,
                [
                    'id_org_name' => $id_org_name,
                    'id_org' => $id_org,
                    'id_discount' => $id_discount
                ]
            );
    }

    public function getPopupDiscountUrlTemplate()
    {
        return '/page/discounts?{id_org_name}={id_org}&id_discount={id_discount}';
    }
}