<?php

class DocumentsView extends AbstractView
{
    /* @var $model DocumentsModel */
    public $model;

    public function setDefaultContent()
    {
        $t = new Template(Core::$config['template_root'] . 'documents' . DIRECTORY_SEPARATOR . 'documents.phtml');
        $this->content = $t->render();
    }

    public function foundation()
    {
        $t = new Template(Core::$config['template_root'] . 'documents' . DIRECTORY_SEPARATOR . 'foundation.phtml');
        $this->content = $t->render();
    }

    public function presentations()
    {
        $t = new Template(Core::$config['template_root'] . 'documents' . DIRECTORY_SEPARATOR . 'presentations.phtml');
        $this->content = $t->render();
    }

    public function trend()
    {
        $t = new Template(Core::$config['template_root'] . 'documents' . DIRECTORY_SEPARATOR . 'trend.phtml');
        $this->content = $t->render();
    }
}