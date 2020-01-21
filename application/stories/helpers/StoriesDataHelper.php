<?php

class StoriesDataHelper
{
    /**
     * ����������� ���������� ������ � ������ "����:������:�������"
     *
     * @param   int     $seconds    ���������� ������
     *
     * @return  string  ����������������� ��������
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
