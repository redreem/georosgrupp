<?php

class Template
{

    protected $phtml = false;
    public $model = null;
    public $view = null;

    public function __construct($template, &$model = null, &$view = null)
    {
        $this->phtml = $template;
        $this->model = $model;
        $this->view = $view;
    }

    public final function render()
    {
        ob_start();

        $model  = !$this->model ? Application::$model   : $this->model;
        $view   = !$this->view  ? Application::$view    : $this->view;

        include $this->phtml;

        $ret = ob_get_contents();

        ob_end_clean();

        return $ret;
    }

}