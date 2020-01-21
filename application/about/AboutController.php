<?php

class AboutController extends AbstractController
{

    /**
     * @var AboutView
     */
    public $view;

    protected function actDefault()
    {
        $this->view->setDefaultContent();
    }

    protected function actMission()
    {
        $this->view->mission();
    }

    protected function actManagement()
    {
        $this->view->management();
    }

    protected function actContacts()
    {
        $this->view->contacts();
    }

    protected function actProduction()
    {
        $this->view->production();
    }
}