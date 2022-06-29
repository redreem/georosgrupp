<?php

namespace Application\Traits;

use Application;
use Core;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

trait CommonDataHelper
{
    use Constants;

    /**
     * склонение к словам
     * пример
     * wordWithNumber(3, ['филиал', 'филиала', 'филиалов']) -> филиала
     * wordWithNumber(5, ['филиал', 'филиала', 'филиалов']) -> филиалов
     * wordWithNumber(5, ['филиал', 'филиала', 'филиалов'], true) -> 5 филиалов
     * @param $n
     * @param $titles
     * @param bool $add_number_to_begin
     * @return mixed
     */
    public static function wordWithNumber($n, $titles, $add_number_to_begin = false)
    {
        $n = intval($n);
        $cases = [2, 0, 1, 1, 1, 2];
        $case_index = min($n % 10, 5);
        if ($case_index < 0) {
            $index = -1;
        } else {
            $index = ($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[$case_index];
        }

        $result = $titles[$index] ?? '';
        if (!empty($result) && $add_number_to_begin) {
            $result = $n . ' ' . $result;
        }

        return $result;
    }

    public static function groupArrayByKey($array, $key)
    {
        $return = [];
        foreach ($array as $val) {
            $return[$val[$key]][] = $val;
        }
        return $return;
    }

    public static function compareFloatAsInt($float_1, $float_2)
    {
        return round($float_1) === round($float_2);
    }

    public static function arraySortBy($field, &$array, $direction = 'asc')
    {
        usort($array, function ($a, $b) use ($field, $direction) {
            $a = $a[$field];
            $b = $b[$field];

            if ($a === $b) return 0;

            $direction = strtolower(trim($direction));
            $directions = [-1, 1];
            if ($direction === 'desc') {
                $directions = [1, -1];
            }
            return ($a < $b) ? $directions[0] : $directions[1];
        });

        return true;
    }

    /**
     * @param $number
     * @param bool $round
     * @param string $currency_text
     * @return string
     */
    public static function formatNumber($number, $round = false, $currency_text = '')
    {
        $decimals = 2;
        $thousands_sep = ' ';
        $dec_point = '.';
        if ($round) {
            $decimals = 0;
            $number = round($number);
        }
        $result = number_format($number, $decimals, $dec_point, $thousands_sep);
        if (!empty($currency_text)) {
            $result = "{$result} {$currency_text}";
        }
        return $result;
    }

    /**
     * @param $string
     * @param int $length
     * @param string $dots
     * @return string
     */
    public static function trimString($string, $length = 10, $dots = '...')
    {
        return strlen($string) > $length ? trim(substr($string, 0, $length)) . $dots : $string;
    }

    /**
     * @param int $gmt
     * @return DateTime
     * @throws Exception
     */
    public static function getCurrentTimeWithGMT($gmt)
    {
        $timezone = new DateTimezone(self::$CONST['DEFAULT_TIMEZONE']);

        if (!is_null($gmt) && $gmt !== 0) {
            $timezoneName = sprintf("%+d", $gmt);
            if ($timezoneName) {
                $timezone = new DateTimezone($timezoneName);
            }
        }

        $original = new DateTime('now', new DateTimeZone('UTC'));
        $modified = $original->setTimezone($timezone);
        return $modified;
    }

    /**
     * Check is array is associative
     * @param $array
     * @return bool
     */
    public static function isArrayAssoc($array)
    {
        $keys = array_keys($array);
        return $keys !== array_keys($keys);
    }

    public static function isOpen($begin, $end, $gmt = 0)
    {
        if (empty($begin) || empty($end)) {
            return false;
        }

        $current_time = self::getCurrentTimeWithGMT($gmt);

        $begin_time = clone $current_time;
        $end_time = clone $current_time;

        $begin_times = [];
        $end_times = [];

        list($begin_times['h'], $begin_times['m'], $begin_times['s']) = explode(':', $begin);
        list($end_times['h'], $end_times['m'], $end_times['s']) = explode(':', $end);

        $begin_time->setTime($begin_times['h'], $begin_times['m'], $begin_times['s']);
        $end_time->setTime($end_times['h'], $end_times['m'], $end_times['s']);

        // if end hour 0, add one day
        if ($end_times['h'] < 1) {
            try {
                $end_time->add(new DateInterval('P1D'));
            } catch (Exception $e) {
                return false;
            }
        }

        if ($current_time > $begin_time && $current_time < $end_time) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $parts
     * @return string
     */
    public static function buildUrl(array $parts)
    {
        $scheme = isset($parts['scheme']) ? ($parts['scheme'] . '://') : '';

        $host = ($parts['host'] ?? '');
        $port = isset($parts['port']) ? (':' . $parts['port']) : '';

        $user = ($parts['user'] ?? '');

        $pass = isset($parts['pass']) ? (':' . $parts['pass']) : '';
        $pass = ($user || $pass) ? "$pass@" : '';

        $path = ($parts['path'] ?? '');
        $query = isset($parts['query']) ? ('?' . $parts['query']) : '';
        $fragment = isset($parts['fragment']) ? ('#' . $parts['fragment']) : '';

        return implode('', [$scheme, $user, $pass, $host, $port, $path, $query, $fragment]);
    }

    public static function isDebugEnvironment($additional_domains = [], $only_additional = false)
    {
        $debug = false;
        $debug_by_domain = false;

        if (Core::$config['debug_mode']) {
            $debug = true;
        }

        $debug_domains = self::$CONST['DEBUG_DOMAINS'];
        if ($only_additional) {
            $debug_domains = $additional_domains;
        } else {
            $debug_domains = array_merge($debug_domains, $additional_domains);
        }
        if (in_array(Application::$base_domain_short, $debug_domains)) {
            $debug_by_domain = true;
        }

        return $debug || $debug_by_domain;
    }


}