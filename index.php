<?php 
//header('Content-type: text/html; charset=utf-8');
//echo 'Сайт находится на техническом обслуживании.';
//die();


define('ROOT_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

define('PUBLIC_DIR', ROOT_DIR . 'public' . DIRECTORY_SEPARATOR);

define('CORE_DIR', ROOT_DIR . 'core' . DIRECTORY_SEPARATOR);

include 'public/index.php';
