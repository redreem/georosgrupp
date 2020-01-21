<?php

namespace Application\Html\Menu;

class Menu
{
    /**
     * HTML builder instance.
     *
     * @var \Application\Html\Menu\HtmlBuilder
     */
    protected $html;
    /**
     * Array of attribtues.
     *
     * @var array
     */
    protected $attributes = [];
    /**
     * Array of menu items.
     *
     * @var array
     */
    protected $items = [];

    protected $wrap_tag = 'ul';

    protected $item_tag = 'li';

    /**
     * Create a new Menu instance.
     *
     * @param  array $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
        $this->html = new HtmlBuilder();
    }

    /**
     * Add a menu item.
     *
     * @param  string $link
     * @param  string $title
     * @param  array $attributes
     * @param bool $active
     * @return \Application\Html\Menu\Menu
     */
    public function add($link, $title, $attributes = [], $active = false)
    {
        $item = (new MenuItem())->link($link)->title($title)->attributes($attributes);
        if ($active) {
            $item->setActive();
        }
        $this->items[] = $item;
        return $this;
    }

    /**
     * Render the menu.
     *
     * @return string
     */
    public function render()
    {
        $attributes = $this->html->attributes($this->attributes);
        $menu[] = "<{$this->wrap_tag}{$attributes}>";
        foreach ($this->items as $item) {
            $menu[] = $item->render($this->item_tag);
        }
        $menu[] = "</{$this->wrap_tag}>";
        return implode(PHP_EOL, $menu);
    }

    public function setAttributes(array $attributes = [])
    {
        $this->attributes = $attributes;
        return $this;
    }
}