<?php

class FE {

    private static $data = [];

    public static function addData($identifier, $data)
    {
        self::$data[$identifier] = $data;
     
    }

    public static function setFEData($data_type='html')
    {

        $data_script = '';
        $script = '';

        if ($data_type=='html') {

            $js_url = ROOT_DIR . 'core' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR;
            $fe_script = file_get_contents($js_url . 'fe.js');

            foreach (self::$data as $identifier => $data) {

                $data_script .= "FE.data['" . $identifier . "'] = ";

                if (is_numeric($data)) {

                    $data_script .= $data;

                } elseif (is_bool($data)) {

                    $data_script .= ($data ? 'true' : 'false');

                } elseif (is_string($data)) {

                    $data_script .= "'" . $data . "'";

                } elseif (is_array($data)) {

                    $data_script .= json_encode($data);

                } else {

                    $data_script .= 'NaN';
                }

                $data_script .= ';' . chr(13) . chr(10);
            }

            $script = '<script>' . $fe_script . chr(13) . chr(10) . $data_script . '</script>';

        } elseif ($data_type=='json') {

            $script = json_encode(self::$data);
        }

        return $script;
    }

}