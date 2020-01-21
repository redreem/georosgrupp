<?php

class DiscountsModuleController extends AbstractController
{

    /**
     * @var DiscountsModuleView
     */
    public $view;

    protected function actDefault()
    {
        $this->view->setDefaultContent();
    }
}