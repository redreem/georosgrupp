<?php

class StoriesDataHelper
{
    /**
     * Форматирует количество секунд в формат "часы:минуты:секунды"
     *
     * @param   int     $seconds    количество секунд
     *
     * @return  string  отформатированное значение
     *
     * @access  public
     * @static
     */
    public static function formatDuration($seconds)
    {
        $seconds = (int)$seconds;

        if ($seconds <= 0) {
            return '';
        }

        $dt1 = new DateTime('@0');
        $dt2 = new DateTime('@' . $seconds);

        $hours = floor($seconds / 3600);
        $hours = $hours > 0 ? $hours . ':' : '';

        return $hours . $dt1->diff($dt2)->format('%I:%S');
    }

}
