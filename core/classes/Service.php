<?php

class Service
{
    private static $data;
    private static $params;
    private static $res;

    public static $FILTER_TYPE_DEFAULT = 0;
    public static $FILTER_TYPE_IMPLODED_INTS = 1;

    public static function filter($data, $type = 0, $params = [])
    {

        self::$data = &$data;
        self::$params = (!empty($params) ? $params : []);

        switch ($type) {
            case 0:
            default:
                self::filterDefault();
                break;

            case self::$FILTER_TYPE_IMPLODED_INTS:
                self::filterTypeImplodedInts();
                break;
        }
        return self::$res;
    }

    private static function filterDefault()
    {
        self::$res = self::$data;
    }

    private static function filterTypeImplodedInts()
    {
        $sep = (empty(self::$params['separator']) ? ',' : self::$params['separator']);
        $arr = explode($sep, self::$data);
        self::$res = implode($sep, $arr);
    }

    public static function empty_dir($directory, $delit)
    {
        if (!$dh = @opendir($directory)) {
            return;
        }
        while (false !== ($obj = readdir($dh))) {

            if($obj=='.' || $obj=='..') {
                continue;
            }

            if (!@unlink($directory.'/'.$obj)) {
                self::empty_dir($directory . '/' . $obj, true);
            }
        }

        closedir($dh);

        if ($delit){
            @rmdir($directory);
        }
    }
}