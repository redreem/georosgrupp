<?php

class DiscountsController extends AbstractController
{

    /**
     * @var DiscountsView
     */
    public $view;

    protected function actDefault()
    {
        $id_discount = $_REQUEST['id_discount'] ?? 0;
        $id_net = $_REQUEST['id_net'] ?? 0;
        $id_firm = $_REQUEST['id_firm'] ?? 0;

        if ($id_discount > 0) {
            $this->view->getDiscountPage();
        } else {
            if ($id_net > 0) {
                $this->view->getNetDiscounts();
            }
            if ($id_firm > 0) {
                $this->view->setDefaultContent();
            }
        }

    }

    protected function actGetdiscount()
    {
        $this->view->getDiscountPopup();
    }

    protected function actSendClickLog()
    {
        $this->view->sendClickLog();
    }

    protected function actShowNews()
    {
        $this->view->showNews();
    }

    protected function actUpdateSocial()
    {
        $this->view->updateSocial();
    }

    protected function actScrolling()
    {
        $this->view->updateScroll();
    }

    protected function actNextPrevArrows()
    {
        $this->view->showArrows();
    }
}