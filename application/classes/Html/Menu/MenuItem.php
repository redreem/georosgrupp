<?php

namespace Application\Html\Menu;

class MenuItem
{
    /**
     * HTML builder instance.
     *
     * @var \Application\Html\Menu\HtmlBuilder
     */
    protected $html;
    /**
     * Menu item title.
     *
     * @var string
     */
    protected $title;
    /**
     * Menu item link.
     *
     * @var string
     */
    protected $link;

    /**
     * Array of attributes.
     *
     * @var array
     */
    protected $attributes = [];

    protected $item_attributes = [];

    protected $menu_active_class = 'firmMenuActive';

    private $active = false;

    /**
     * Create a new menu item instance.
     *
     */
    public function __construct()
    {
        $this->html = new HtmlBuilder;
    }

    /**
     * Set the menu item link.
     *
     * @param  string $link
     * @return \Application\Html\Menu\MenuItem
     */
    public function link($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * Set the menu item title.
     *
     * @param $title
     * @return \Application\Html\Menu\MenuItem
     */
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set the menu item attributes.
     *
     * @param  array $attributes
     * @return \Application\Html\Menu\MenuItem
     */
    public function attributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Make Item active
     */
    public function setActive()
    {
        $this->active = true;
        $attributes = $this->item_attributes;
        $class = $attributes['class'] ?? '';
        $class .= $this->menu_active_class;
        $attributes['class'] = $class;
        $this->item_attributes = $attributes;
    }

    /**
     * Render the menu item.
     *
     * @param string $tag
     * @return string
     */
    public function render($tag = 'li')
    {
        $attributes = $this->html->attributes($this->item_attributes);
        $link = $this->html->link($this->link, $this->title, $this->attributes);
        if ($this->active) {
            $link = $this->title;
        }
        return "<{$tag}{$attributes}>" . $link . "</{$tag}>";
    }
}