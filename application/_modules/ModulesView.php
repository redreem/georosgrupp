<?php

class ModulesView extends AbstractView
{
    public $modules;

    public function __construct(&$modules)
    {
        $this->modules = $modules;
    }
}