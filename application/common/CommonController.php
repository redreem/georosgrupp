<?php

class CommonController extends AbstractController
{

    /**
     * @var CommonView
     */
    public $view;

    protected function actDefault()
    {
        $this->view->showPage();
    }

}