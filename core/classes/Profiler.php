<?php

class Profiler {

    static $show_profile = false;
    static $show_profile_cookie = 'show_profile';
    static $current_level = 0;
    static $profiler_items = [];
    static $error_handler = false;

    static $alias_groups = [
        'DB Query'  => [ 'time'  => 0, 'color' => 'faf',],
        'TEMPLATE'  => [ 'time'  => 0, 'color' => 'aff',],
        'FUNC'      => [ 'time'  => 0, 'color' => 'ffa',],
        'CODE'      => [ 'time'  => 0, 'color' => 'f84',],
    ];

    static $alias_setting = [

        'total' => [
            'description' => 'Общее время',
            'color' => 'afa',
            'group' => '',
        ],

        'template_render' => [
            'description' => 'Template render',
            'color' => 'aff',
            'group' => 'TEMPLATE',
        ],

        'dbase' => [
            'description' => 'AVE_DB-query',
            'color' => 'faf',
            'group' => 'DB Query',
        ],

        'func' => [
            'description' => 'Function',
            'color' => 'ffa',
            'group' => 'FUNC',
        ],

        'code' => [
            'description' => 'Inline Code',
            'color' => 'f84',
            'group' => 'CODE',
        ],
    ];

    static $id = 0;
    static $id_total = 0;
    static $time = [];

    public static function start()
    {

        if ( isset($_COOKIE[ self::$show_profile_cookie ]) && $_COOKIE[ self::$show_profile_cookie ] == 1) {
            define('SHOW_PROFILE',1);
            self::$show_profile = true;
        } else {
            define('SHOW_PROFILE',0);
            return;
        }

        self::$id_total = self::start_timer('total','-');
    }

    static private function compareItems($a,$b) {

        $v1 = self::$profiler_items[$a]['time'];
        $v2 = self::$profiler_items[$b]['time'];

        if ($v1 != $v2) {

            return ($v1 < $v2) ? 1 : -1;
        } else {

            return 0;
        }
    }

    public static function finish() {

        if (!self::$show_profile) {

            return;
        }

        self::stop_timer(self::$id_total);

        $total_time = self::$time[self::$id_total]['e'] - self::$time[self::$id_total]['s'];
        $items_time = 0;

        $html = '<div class="profiler_transport">';

        $cnt = 0;
        foreach (self::$time as $id => $item) {

            $item_html = '<span class="profiler_item">';
            $item_time = round( ($item['e'] - $item['s']), 5 );

            $line_style = ' style="background-color:#' . self::$alias_setting[ $item['a'] ]['color'] . ( $item['a'] == 'dbase' ? ';cursor:pointer;' : '' ) . '"';
            $div_style = ' style="padding-left:' . $item['l']*10 . 'px;"';
            $info_click = ( $item['a'] == 'dbase' ? ' onclick="profiler.infoClick(this)"' : '' );

            if ($id != self::$id_total) {
                $cnt++;
                $cnt_str = $cnt . ': ';
            } else {
                $cnt_str = '';
            }

            $item_html .= '<span class="profiler_desc"' . $line_style . '><div' . $div_style . '>' . $cnt_str . self::$alias_setting[ $item['a'] ]['description'] . '</div></span>';
            $item_html .= '<span class="profiler_info"' . $line_style . $info_click . '>' . $item['i'] . '</span>';
            $item_html .= '<span class="profiler_time_t"' . $line_style . '>' . $item_time . '</span>';

            $percent = ($item['l'] < 3 ? round( 100 * (($item['e'] - $item['s'])/$total_time), 1 ) . '%' : '-');
            $item_html .= '<span class="profiler_time_percent"' . $line_style . '>' . $percent . '</span>';
            $item_html .= '</span>';

            if ($id != self::$id_total && $item['l'] == 2) {
                $items_time += ($item['e'] - $item['s']);
                //self::$alias_groups[ $item['a'] ]['time'] += ($item['e'] - $item['s']); //ошибка в выборке массива
            }

            self::$profiler_items[] = ['time' => $item_time, 'html' => $item_html];
        }

        //uksort(  self::$profiler_items, "self::compareItems");

        foreach (self::$profiler_items as $item) {

            $html .= $item['html'];
        }

        //неучтенное время
        $html .= '<span class="profiler_item">';
        $line_style = ' style="background-color:#aaa"';
        $html .= '<span class="profiler_desc"' . $line_style . '>Не учтенное время</span>';
        $html .= '<span class="profiler_info"' . $line_style . '>-</span>';
        $html .= '<span class="profiler_time_t"' . $line_style . '>' . round($total_time - $items_time, 5) . '</span>';
        $html .= '<span class="profiler_time_percent"' . $line_style . '>' . round( 100 * (($total_time - $items_time)/$total_time), 1 ) . '%</span>';
        $html .= '</span>';

        $html .= '</div>';
        echo $html;
        Errors::render_console();
    }

    public static function start_timer($alias, $add_info) {

        if (!self::$show_profile) {
            return 0;
        }

        self::$id++;
        self::$time[self::$id]['a'] = $alias;
        self::$time[self::$id]['i'] = $add_info;
        self::$time[self::$id]['s'] = self::get_microtime();
        self::$current_level++;
        self::$time[self::$id]['l'] = self::$current_level;
        return self::$id;
    }

    public static function stop_timer($id) {
        if (!self::$show_profile) {
            return;
        }
        self::$time[$id]['e'] = self::get_microtime();
        self::$current_level--;
        return round( self::$time[$id]['e'] - self::$time[$id]['s'], 5);
    }

    public static function get_microtime() {
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        return $mtime;
    }

}