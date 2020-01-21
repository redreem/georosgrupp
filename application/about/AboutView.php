<?php

class AboutView extends AbstractView
{
    /* @var $model AboutModel */
    public $model;

    public function setDefaultContent()
    {
        $t = new Template(Core::$config['template_root'] . 'about' . DIRECTORY_SEPARATOR . 'about.phtml');
        $this->content = $t->render();
    }

    public function mission()
    {
        $t = new Template(Core::$config['template_root'] . 'about' . DIRECTORY_SEPARATOR . 'mission.phtml');
        $this->content = $t->render();
    }

    public function management()
    {
        $t = new Template(Core::$config['template_root'] . 'about' . DIRECTORY_SEPARATOR . 'management.phtml');
        $this->content = $t->render();
    }

    public function contacts()
    {
        $t = new Template(Core::$config['template_root'] . 'about' . DIRECTORY_SEPARATOR . 'contacts.phtml');
        $this->content = $t->render();
    }

    public function production()
    {
        $t = new Template(Core::$config['template_root'] . 'about' . DIRECTORY_SEPARATOR . 'production.phtml');
        $this->content = $t->render();
    }
}