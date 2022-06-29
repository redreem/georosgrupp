<?php

include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Image.php';

class ImageModule
{

    const RESIZE_TYPE_CROP = 1;
    const RESIZE_TYPE_SET_WIDTH = 2;
    const RESIZE_TYPE_SET_HEIGTH = 3;

    const SOURCE_TYPE_IS_PATH = 1;
    const SOURCE_TYPE_IS_URL = 2;

    static $types = ['', 'gif', 'jpeg', 'png'];

    public static function resize($img_file, $resize_type, $params)
    {
        switch ($resize_type) {

            case self::RESIZE_TYPE_CROP:
                break;

            case self::RESIZE_TYPE_SET_WIDTH:
                break;

            case self::RESIZE_TYPE_SET_HEIGTH:
                break;

            default:
                break;
        }
    }

    public static function get_auto_crop_area($img, $area_aspect_ratio)
    {

        $img_aspect_ratio = $img['w'] / $img['h'];

        if ($area_aspect_ratio < $img_aspect_ratio) {

            $area = [
                'w' => $img['h'] * $area_aspect_ratio,
                'h' => $img['h'],
                't' => 0,
                'l' => ($img['w'] - ($img['h'] * $area_aspect_ratio)) / 2
            ];
        } else {

            $area = [
                'w' => $img['w'],
                'h' => $img['w'] / $area_aspect_ratio,
                't' => ($img['h'] - ($img['w'] / $area_aspect_ratio)) / 2,
                'l' => 0
            ];
        }
        return $area;
    }

    public static function create_crop_image($path, $img_file, $date_moder, $cache_id, $width = false, $height = false, $source_type = self::SOURCE_TYPE_IS_PATH)
    {

        if ($source_type === self::SOURCE_TYPE_IS_URL) {

            $tmp_path = ROOT_DIR . 'public' . DIRECTORY_SEPARATOR . 'prod_src' . DIRECTORY_SEPARATOR . 'img_cache' . DIRECTORY_SEPARATOR;
            $tmp_file = md5($img_file . $date_moder);

            $remote_url = $path . $img_file;
            if (!filter_var($remote_url, FILTER_VALIDATE_URL)) {
                $remote_url = 'https:' . $remote_url;
            }

            if (self::get_remote_image($remote_url, $tmp_path . $tmp_file) > 0) {

                return $path . $img_file;
            }

            $path = $tmp_path;
            $img_file = $tmp_file;
        }

        $cache_file = md5($path . $img_file . $date_moder . $cache_id . ($width ? 0 : $width) . ($height ? $height : 0));

        $cache_dir_relative = '/' . str_replace(DIRECTORY_SEPARATOR, '/', Core::$config['img']['cache_dir']) . ($cache_id ? $cache_id . '/' : '') .
            substr($cache_file, 0, 2) . '/' .
            substr($cache_file, 2, 2) . '/' .
            substr($cache_file, 4, 2) . '/';

        $cache_dir_absolute = ROOT_DIR . 'public' . DIRECTORY_SEPARATOR . Core::$config['img']['cache_dir'] . ($cache_id ? $cache_id . DIRECTORY_SEPARATOR : '') .
            substr($cache_file, 0, 2) . DIRECTORY_SEPARATOR .
            substr($cache_file, 2, 2) . DIRECTORY_SEPARATOR .
            substr($cache_file, 4, 2) . DIRECTORY_SEPARATOR;

        if (!file_exists($cache_dir_absolute)) {

            mkdir($cache_dir_absolute, 0777, true);
        }

        $src_file = $path . $img_file;

        list($w, $h, $type) = getimagesize($src_file);

        $img = [
            'w' => $w,
            'h' => $h,
        ];

        $dst_file = $cache_dir_absolute . $cache_file . '.' . self::$types[$type];

        $dest_file_name = $cache_file . '.' . self::$types[$type];

        if (!file_exists($dst_file)) {

            /*
            $res_img = ['w' => 468, 'h' => 263];

            $area_aspect_ratio = $res_img['w'] / $res_img['h'];

            $area = self::get_auto_crop_area($img, $area_aspect_ratio);

            $area_percent = [
                100 * $area['l'] / $img['w'],
                100 * $area['t'] / $img['h'],
                100 * $area['w'] / $img['w'],
                100 * $area['h'] / $img['h'],
            ];
            */
            $res_img = [
                'w' => $width,
                'h' => $height,
            ];

            Image::resize($src_file, $cache_dir_absolute . $dest_file_name, $res_img['w'], $res_img['h']);
        }

        return $cache_dir_relative . $dest_file_name;
    }

    private static function get_remote_image($url, $save_path)
    {
        if (file_exists($save_path)) {
            return;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $raw = curl_exec($ch);

        if (curl_errno($ch) > 0) {

            return curl_errno($ch);
        }
        curl_close($ch);

        $fp = fopen($save_path, 'x');
        fwrite($fp, $raw);
        fclose($fp);

        return 0;
    }
}