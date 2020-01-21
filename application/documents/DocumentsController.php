<?php

class DocumentsController extends AbstractController
{

    /**
     * @var DocumentsView
     */
    public $view;

    protected function actDefault()
    {
        $this->view->setDefaultContent();
    }

    protected function actFoundation()
    {
        $this->view->foundation();
    }

    protected function actPresentations()
    {
        $this->view->presentations();
    }

    protected function actTrend()
    {
        $this->view->trend();
    }

}