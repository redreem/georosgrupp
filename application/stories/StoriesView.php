<?php

class StoriesView extends AbstractView
{
    /* @var $model StoriesModel */
    public $model;
    public $breadcrumbs = '';
    public $firm_menu;
    public $is_popup;
    public $firm_menu_active = 'stories';

    public $news_module_content;
    public $discounts_module_content;
    public $social_module_content;

    public function setDefaultContent()
    {
        $t = new Template(Core::$config['template_root'] . '_parts' . DIRECTORY_SEPARATOR . 'firm_menu' . DIRECTORY_SEPARATOR . 'firm_menu.phtml');
        $this->firm_menu = $t->render();

        $social_module = new Modules('social', ['action' => 'getTemplate']);
        $this->social_module_content = $social_module->content;

        $t = new Template(Core::$config['template_root'] . 'stories' . DIRECTORY_SEPARATOR . 'stories_head.phtml');
        $this->content = $t->render();

        $t = new Template(Core::$config['template_root'] . 'stories' . DIRECTORY_SEPARATOR . 'stories_load.phtml');
        $this->content .= $t->render();

        $t = new Template(Core::$config['template_root'] . 'stories' . DIRECTORY_SEPARATOR . 'stories_foot.phtml');
        $this->content .= $t->render();

        $t = new Template(Core::$config['template_root'] . '_parts' . DIRECTORY_SEPARATOR . 'breadcrumbs' . DIRECTORY_SEPARATOR . 'breadcrumbs.phtml');
        $this->breadcrumbs = $t->render();
    }

    public function getStoryPopup()
    {
        $this->is_popup = true;
        $t = new Template(Core::$config['template_root'] . DIRECTORY_SEPARATOR . 'stories' . DIRECTORY_SEPARATOR . 'stories_popup.phtml');
        $this->content = $t->render();
    }

    public function getStoryPage()
    {
        $this->is_popup = false;
        $t = new Template(Core::$config['template_root'] . DIRECTORY_SEPARATOR . 'stories' . DIRECTORY_SEPARATOR . 'stories_popup.phtml');
        $this->content = $t->render();
    }

    public function getStoryLoad()
    {
        $t = new Template(Core::$config['template_root'] . DIRECTORY_SEPARATOR . 'stories' . DIRECTORY_SEPARATOR . 'stories_load.phtml');
        $this->content = $t->render();
    }

}