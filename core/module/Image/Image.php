<?php

class Image
{

    const ERROR_NO_SIZE = -1;//Невозможно получить длину или ширину изображения
    const ERROR_INCORRECT_IMAGE = -2;//Некорректный формат файла

    const SUCCESS = true;
    const ERROR = false;

    static $result_code;

    static $types = ['','gif','jpeg','png'];

    public static function resize($file_input, $file_output, $w_o, $h_o, $percent = false) {

        $img_data = self::getImage($file_input);

        if ($img_data == self::ERROR) {

            return self::ERROR;
        }

        if ($percent) {

            $w_o *= $img_data['width'] / 100;
            $h_o *= $img_data['height'] / 100;
        }

        if (!$h_o) {

            $h_o = $w_o / ($img_data['width'] / $img_data['height']);
        }

        if (!$w_o) {

            $w_o = $h_o / ($img_data['height'] / $img_data['width']);
        }

        $img_o = imagecreatetruecolor($w_o, $h_o);

        // add transparency
        if($img_data['extension'] === 'png'){
            imagealphablending($img_o, false);
            imagesavealpha($img_o,true);

            $transparent = imagecolorallocatealpha($img_o, 255, 255, 255, 127);
            imagefilledrectangle($img_o, 0, 0, $w_o, $h_o, $transparent);
        }

        imagecopyresampled($img_o, $img_data['img'], 0, 0, 0, 0, $w_o, $h_o, $img_data['width'], $img_data['height']);

        if ($img_data['type'] == 2) {

            return imagejpeg($img_o, $file_output, 100);
        } else {

            $func = 'image'.$img_data['extension'];

            return $func($img_o, $file_output);
        }
    }

    public static function crop($file_input, $file_output, $crop, $percent = false) {

        $img_data = self::getImage($file_input);

        if ($img_data == self::ERROR) {

            return self::ERROR;
        }

        list($x_o, $y_o, $w_o, $h_o) = $crop;

        if ($percent) {
            $x_o = $x_o * $img_data['width'] / 100;
            $y_o = $y_o * $img_data['height'] / 100;
            $w_o = $w_o * $img_data['width'] / 100;
            $h_o = $h_o * $img_data['height'] / 100;
        }

        $img_o = imagecreatetruecolor($w_o, $h_o);

        imagecopy($img_o, $img_data['img'], 0, 0, $x_o, $y_o, $w_o, $h_o);

        if ($img_data['type'] == 2) {

            return imagejpeg($img_o,$file_output,100);
        } else {

            $func = 'image'.$img_data['extension'];

            return $func($img_o,$file_output);
        }
    }

    private static function getImage($file_input)
    {
        self::$result_code = self::SUCCESS;

        list($w, $h, $type) = getimagesize($file_input);

        if (!$w || !$h) {

            self::$result_code = self::ERROR_NO_SIZE;
            return self::ERROR;
        }

        $extension = self::$types[$type];

        if ($extension) {

            $func = 'imagecreatefrom'.$extension;
            $img = $func($file_input);

        } else {

            self::$result_code = self::ERROR_INCORRECT_IMAGE;
            return self::ERROR;
        }

        return [
            'width'     => $w,
            'height'    => $h,
            'type'      => $type,
            'extension' => $extension,
            'img'       => $img,
        ];
    }
}