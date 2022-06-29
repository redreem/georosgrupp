<?php

abstract class AbstractController
{

    /**
     * @var AbstractModel
     */
    public $model;

    /**
     * @var AbstractView
     */
    public $view;

    public $action;
    public $sub;
    public $id;

    public function __construct(&$model, &$view, $action = '')
    {
        $this->model = $model;
        $this->model->setController($this);

        $this->view = $view;
        $this->view->setController($this);
        $this->view->setModel($this->model);

        $this->action = (!empty($action) ? $action : Application::$action);

        $this->setHelpers();

        if ($this->action != '') {
            $this->execAction();
        } else {
            $this->doDefault();
        }
    }

    protected function execAction()
    {
        $method_name = "act" . ucfirst($this->action);

        if (method_exists($this, $method_name)) {
            $this->$method_name();
        }
    }

    protected function setHelpers()
    {

    }

    protected function doDefault()
    {

    }

}