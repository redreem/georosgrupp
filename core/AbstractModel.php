<?php

abstract class AbstractModel
{

    /**
     * @var AbstractController
     */
    public $controller;

    public function __construct()
    {
        $this->dataProcess();
    }

    public function setController(&$controller)
    {
        $this->controller = $controller;
    }

    protected function dataProcess()
    {

    }

}