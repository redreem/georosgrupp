<?php

class Page404View extends AbstractView
{
    /* @var $model Page404Model */
    public $model;
    public $breadcrumbs = '';
    public $news_content = '';

    public function setDefaultContent()
    {
        $t = new Template(Core::$config['template_root'] . '_parts' . DIRECTORY_SEPARATOR . 'breadcrumbs' . DIRECTORY_SEPARATOR . 'breadcrumbs.phtml');
        $this->breadcrumbs = $t->render();

        $news_module = new Modules('news',
            [
                'limit' => '0-5',
                'img' => 1
            ]
        );

        $this->news_content = $news_module->content;

        $t = new Template(Core::$config['template_root'] . 'page404' . DIRECTORY_SEPARATOR . 'page404.phtml');
        $this->content = $t->render();
    }

}