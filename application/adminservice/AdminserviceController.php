<?php

class AdminserviceController extends AbstractController
{

    /**
     * @var AdminserviceView
     */
    public $view;

    protected function actDefault()
    {
        $this->view->setDefaultContent();
    }
}