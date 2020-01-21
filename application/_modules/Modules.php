<?php

class Modules
{

    public $content;
    public $param;
    public $action;

    public $module_model;
    public $module_view;
    private $module_controller;

    private $module_name;

    private $module_model_name;
    private $module_view_name;
    private $module_controller_name;

    public function __construct($module_name, $param = [], $action = 'default')
    {

        $this->param = $param;
        $this->module_name = trim($module_name);
        $this->action = $action;

        $module_class = $this->getModuleClassName();
        $this->module_model_name      = $module_class . 'ModuleModel';
        $this->module_view_name       = $module_class . 'ModuleView';
        $this->module_controller_name = $module_class . 'ModuleController';

        include_once 'ModulesModel.php';
        include_once 'ModulesView.php';

        include_once $module_name . DIRECTORY_SEPARATOR . $this->module_model_name . '.php';
        $view_file = $module_name . DIRECTORY_SEPARATOR . $this->module_view_name . '.php';
        $dir = dirname(__FILE__);
        $view_real_path = realpath($dir . '/' . $view_file);

        if (file_exists($view_real_path)) {
            include_once $view_real_path;
            $this->module_view = new $this->module_view_name($this);
        } else {
            $this->module_view = new ModulesView($this);
        }

        include_once $module_name . DIRECTORY_SEPARATOR . $this->module_controller_name . '.php';

        $this->module_model = new $this->module_model_name($this);
        $this->module_controller = new $this->module_controller_name($this->module_model, $this->module_view,  $this->action);

        $this->content = $this->module_view->content;
    }

    public function getModel()
    {
        if (is_object($this->module_model)) {
            $model = $this->module_model;
        } else {
            $model = new $this->module_model_name($this);;
        }
        return $model;
    }

    public function getView()
    {
        if (is_object($this->module_view)) {
            $view = $this->module_view;
        } else {
            $view = new $this->module_view_name($this);;
        }
        return $view;
    }

    private function getModuleClassName()
    {
        $up_first_letter = ucwords($this->module_name,'_');
        return str_replace('_', '', $up_first_letter);
    }

}