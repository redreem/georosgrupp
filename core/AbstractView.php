<?php

abstract class AbstractView
{

    /**
     * @var AbstractController
     */
    public $controller;

    /**
     * @var AbstractModel
     */
    public $model;

    public $content;

    public function setController(&$controller)
    {
        $this->controller = $controller;
    }

    public function setModel(&$model)
    {
        $this->model = $model;
    }

}