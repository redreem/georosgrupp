<?php

class CommonView extends AbstractView
{

    /* @var $model CommonModel */
    public $model;

    public $content = [];

    public $parts_root;

    public $head;
    public $nav = '';
    public $footer = '';
    public $script_section = '';

    public $share_links_content = '';

    public function showPage()
    {
        $this->parts_root = Core::$config['template_root'] . '_parts' . DIRECTORY_SEPARATOR;

        $t = new Template($this->parts_root . 'head' . DIRECTORY_SEPARATOR . 'head.phtml');
        $this->head = $t->render();

        if (!$this->model->no_nav) {
            $t = new Template($this->parts_root . 'nav' . DIRECTORY_SEPARATOR . 'nav.phtml', $this->model, $this);
            $this->nav = $t->render();
        }

        if (!$this->model->no_footer) {
            $t = new Template($this->parts_root . 'footer' . DIRECTORY_SEPARATOR . 'footer.phtml');
            $this->footer = $t->render();
        }

        $t = new Template($this->parts_root . 'footer' . DIRECTORY_SEPARATOR . 'script_section.phtml');
        $this->script_section = $t->render();

        $t = new Template(Core::$config['template_root'] . 'layout' . DIRECTORY_SEPARATOR . 'layout.phtml');
        $this->content = $t->render();
    }

}