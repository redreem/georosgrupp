<?php

class AdminserviceView extends AbstractView
{
    /* @var $model AdminserviceModel */
    public $model;

    public $breadcrumbs;

    public function setDefaultContent()
    {
        $t = new Template(Core::$config['template_root'] . 'adminservice' . DIRECTORY_SEPARATOR . 'adminservice.phtml');
        $this->content = $t->render();
    }
}