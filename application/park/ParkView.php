<?php

class ParkView extends AbstractView
{
    /* @var $model ParkModel */
    public $model;

    public function setDefaultContent()
    {
        $t = new Template(Core::$config['template_root'] . 'park' . DIRECTORY_SEPARATOR . 'park.phtml');
        $this->content = $t->render();
    }

    public function focus()
    {
        $t = new Template(Core::$config['template_root'] . 'park' . DIRECTORY_SEPARATOR . 'focus.phtml');
        $this->content = $t->render();
    }

    public function news()
    {
        $t = new Template(Core::$config['template_root'] . 'park' . DIRECTORY_SEPARATOR . 'news.phtml');
        $this->content = $t->render();
    }

    public function partners()
    {
        $t = new Template(Core::$config['template_root'] . 'park' . DIRECTORY_SEPARATOR . 'partners.phtml');
        $this->content = $t->render();
    }

}