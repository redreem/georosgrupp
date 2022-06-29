<?php

class HomeView extends AbstractView
{
    /* @var $model HomeModel */
    public $model;
    public $breadcrumbs = '';

    public $resized_img_by_width;
    public $resized_img_by_height;

    public function setDefaultContent()
    {

        $t = new Template(Core::$config['template_root'] . 'home' . DIRECTORY_SEPARATOR . 'home.phtml');
        $this->content = $t->render();
    }

    public function getFE()
    {
        $this->content = 'Test content';
    }

    public function getGeo()
    {
        $t = new Template(Core::$config['template_root'] . 'home' . DIRECTORY_SEPARATOR . 'home_popup.phtml', $this->model, $this);
        $this->content = $t->render();
        $this->content = iconv('windows-1251', 'utf-8', $t->render());

        $this->content = json_encode([
            'content' => $this->content
        ]);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                //echo ' - Ошибок нет';
                break;
            case JSON_ERROR_DEPTH:
                echo ' - Достигнута максимальная глубина стека';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                echo ' - Некорректные разряды или не совпадение режимов';
                break;
            case JSON_ERROR_CTRL_CHAR:
                echo ' - Некорректный управляющий символ';
                break;
            case JSON_ERROR_SYNTAX:
                echo ' - Синтаксическая ошибка, не корректный JSON';
                break;
            case JSON_ERROR_UTF8:
                echo ' - Некорректные символы UTF-8, возможно неверная кодировка';
                break;
            default:
                echo ' - Неизвестная ошибка';
                break;
        }

    }

}