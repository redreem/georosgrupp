<?php

class ParkController extends AbstractController
{

    /**
     * @var ParkView
     */
    public $view;

    protected function actDefault()
    {
        $this->view->setDefaultContent();
    }

    protected function actFocus()
    {
        $this->view->focus();
    }

    protected function actNews()
    {
        $this->view->news();
    }

    protected function actPartners()
    {
        $this->view->partners();
    }
}