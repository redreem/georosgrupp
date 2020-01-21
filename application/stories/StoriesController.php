<?php

class StoriesController extends AbstractController
{

    /**
     * @var StoriesView
     */
    public $view;

    protected function actDefault()
    {
        if (empty($_REQUEST['id_story'])) {

            $this->view->setDefaultContent();
        } else {

            $this->view->getStoryPage();
        }
    }

    protected function actGetStory()
    {
        $this->view->getStoryPopup();
    }

    protected function actLike()
    {
    }

    protected function actLoad()
    {
        $this->view->getStoryLoad();
    }

    protected function actShowNews()
    {
        $this->view->showNews();
    }

    protected function actUpdateSocial()
    {
        $this->view->updateSocial();
    }

}