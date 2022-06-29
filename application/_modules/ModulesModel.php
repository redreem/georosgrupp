<?php

class ModulesModel extends AbstractModel
{
    public $modules;

    public function __construct(&$modules)
    {
        $this->modules = $modules;
        parent::__construct();
    }
}