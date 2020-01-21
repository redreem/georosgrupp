<?php

class DiscountsModuleView extends ModulesView
{
    /* @var $model DiscountsModuleModel */

    public $model;

    public function setDefaultContent()
    {

        $t = new Template(
            Core::$config['template_root'] . '_modules' . DIRECTORY_SEPARATOR . 'discounts' . DIRECTORY_SEPARATOR . 'discounts.phtml',
            $this->model,
            $this
        );

        $this->content = $t->render();
    }
}