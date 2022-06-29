<?php

namespace Application\Traits;

use Application;

trait PricesDataHelper
{

    public static function prepareItems(array $items, array $options = [])
    {
        $main_domain = Application::$main_domain;
        $result = $items;

        foreach ($items as $k => $item) {
            $img = '';
            if ($item['img_extension']) {
                $extension = $item['img_extension'];
                $img = "{$main_domain}price_img/{$item['month_add']}/{$item['id_item_fix']}.$extension";
            }
            $result[$k]['image'] = $img;

            $average = ($item['max_price'] + $item['min_price']) / 2;
            $result[$k]['average'] = $average;

            $price_color = 'grey';

            $percent = self::calculatePercentage($item['price_item_price'], $average);

            if ($percent > 20 && $percent < 70) {
                $price_text = 'Средняя цена в городе';
                $price_color = 'green';
            } else {
                if ($item['price_item_price'] > $average) {
                    $price_text = 'Выше средней цены в городе';
                } else {
                    $price_text = 'Очень низкая цена';
                }
            }

            $price_item_price = self::formatNumber($item['price_item_price'], $options['need_round']);

            $result[$k]['price_item_price'] = $price_item_price;

            $result[$k]['price_text'] = $price_text;
            $result[$k]['price_color'] = $price_color;
            $result[$k]['percent'] = $percent;
            $result[$k]['has_image'] = boolval($result[$k]['image']);
        }
        return $result;
    }

    public static function calculatePercentage($old_figure, $new_figure)
    {
        if ($old_figure < 1) {
            $old_figure = 1;
        }
        $percentChange = (($old_figure - $new_figure) / $old_figure) * 100;
        return round(abs($percentChange));
    }

}