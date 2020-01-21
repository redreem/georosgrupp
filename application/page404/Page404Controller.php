<?php

class Page404Controller extends AbstractController
{

    /**
     * @var Page404View
     */
    public $view;

    protected function actDefault()
    {
        $this->view->setDefaultContent();
    }

}