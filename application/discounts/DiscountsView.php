<?php

class DiscountsView extends AbstractView
{
    /* @var $model DiscountsModel */
    public $model;
    public $breadcrumbs = '';
    public $firm_menu;
    public $firm_menu_active = 'discounts';
    public $is_popup;

    public $news_module_content;
    public $discounts_module_content;

    public $social_module_content;
    public $social_module_mobile_content;
    public $body_class;

    public $discounts_blocks;

    public function setDefaultContent()
    {
        $t = new Template(Core::$config['template_root'] . '_parts' . DIRECTORY_SEPARATOR . 'firm_menu' . DIRECTORY_SEPARATOR . 'firm_menu.phtml');
        $this->firm_menu = $t->render();

        $social_module = new Modules('social', ['action' => 'getTemplate']);
        $this->social_module_content = $social_module->content;

        $social_module = new Modules('social', ['action' => 'social_mobile']);
        $this->social_module_mobile_content = $social_module->content;

        $discounts_module = new Modules('discounts');
        $this->discounts_module_content = $discounts_module->content;

        $t = new Template(Core::$config['template_root'] . 'discounts' . DIRECTORY_SEPARATOR . 'discounts_blocks.phtml');
        $this->discounts_blocks = $t->render();

        $t = new Template(Core::$config['template_root'] . 'discounts' . DIRECTORY_SEPARATOR . 'discounts.phtml');
        $this->content = $t->render();

        $t = new Template(Core::$config['template_root'] . '_parts' . DIRECTORY_SEPARATOR . 'breadcrumbs' . DIRECTORY_SEPARATOR . 'breadcrumbs.phtml');
        $this->breadcrumbs = $t->render();

    }

    public function getNetDiscounts()
    {
        $t = new Template(Core::$config['template_root'] . '_parts' . DIRECTORY_SEPARATOR . 'firm_menu' . DIRECTORY_SEPARATOR . 'firm_menu_discounts_net.phtml');
        $this->firm_menu = $t->render();

        $social_module = new Modules('social', ['action' => 'getTemplate']);
        $this->social_module_content = $social_module->content;

        $social_module = new Modules('social', ['action' => 'social_mobile']);
        $this->social_module_mobile_content = $social_module->content;

        $discounts_module = new Modules('discounts');
        $this->discounts_module_content = $discounts_module->content;

        $t = new Template(Core::$config['template_root'] . 'discounts' . DIRECTORY_SEPARATOR . 'discounts_net.phtml');
        $this->content = $t->render();

        $t = new Template(Core::$config['template_root'] . '_parts' . DIRECTORY_SEPARATOR . 'breadcrumbs' . DIRECTORY_SEPARATOR . 'breadcrumbs.phtml');
        $this->breadcrumbs = $t->render();
    }

    public function getDiscountPopup()
    {
        $ds = DIRECTORY_SEPARATOR;
        $this->is_popup = true;

        $t = new Template(Core::$config['template_root'] . '_parts' . $ds . 'breadcrumbs' . $ds . 'breadcrumbs.phtml');
        $this->breadcrumbs = $t->render();
        $popup_template = 'discounts_popup';
        if ($this->model->id_net > 0) {
            $popup_template = 'discounts_net_popup';
        }

        $t = new Template(Core::$config['template_root'] . $ds . 'discounts' . $ds . $popup_template . '.phtml');
        $this->content = $t->render();
    }

    public function getDiscountPage()
    {
        $ds = DIRECTORY_SEPARATOR;
        $this->is_popup = false;
        $this->body_class = 'popup_page discounts';

        $t = new Template(Core::$config['template_root'] . '_parts' . $ds . 'breadcrumbs' . $ds . 'breadcrumbs.phtml');
        $this->breadcrumbs = $t->render();

        $popup_template = 'discounts_popup';
        if ($this->model->id_net > 0) {
            $popup_template = 'discounts_net_popup';
        }

        $t = new Template(Core::$config['template_root'] . $ds . 'discounts' . $ds . $popup_template . '.phtml');
        $this->content = $t->render();
    }

    public function sendClickLog()
    {
        if (!$this->model->discount_data['url_skidki']) {
            $res = 0;
        } else {
            $res = 1;
        }
        $this->content = json_encode([
            'res' => $res,
        ]);
    }

    public function updateScroll()
    {
        $t = new Template(Core::$config['template_root'] . 'discounts' . DIRECTORY_SEPARATOR . 'discounts_blocks.phtml');
        $this->content = $t->render();
    }

    public function showArrows()
    {
        $this->content = $this->model->arrows_content;
    }

    public function updateSocial()
    {
        $social_module = new Modules('social', ['action' => 'update']);
        $this->content = json_encode([
            'social' => $social_module->getModel()->click_count,
        ]);
    }

}