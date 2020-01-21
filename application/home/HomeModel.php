<?php

class HomeModel extends AbstractModel
{
    public $device_type;
    public $breadcrumbs;

    protected function dataProcess()
    {

        $this->breadcrumbs = [

        ];

        switch (Application::$action) {

            case 'default':
            default:

                Module::load('mobiledetect');
                MobiledetectModule::init();
                MobiledetectModule::detect();

                $this->device_type = (MobiledetectModule::$is_mobile ? 'Phone' : (MobiledetectModule::$is_tablet ? 'Tablet' : 'Desktop'));

                break;
        }
    }
}