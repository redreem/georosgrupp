<?php

class CaptchaModuleController extends AbstractController
{

    /**
     * @var CaptchaModuleView
     */
    public $view;

    protected function actDefault()
    {
        $this->view->setDefaultContent();
    }
}