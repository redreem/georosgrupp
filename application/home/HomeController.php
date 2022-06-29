<?php

class HomeController extends AbstractController
{

    /**
     * @var HomeView
     */
    public $view;

    protected function actDefault()
    {
        $this->view->setDefaultContent();
    }

    protected function actGetgeo()
    {
        $this->view->getGeo();
    }

    protected function actGetfe()
    {
        $this->view->getFE();
    }
}