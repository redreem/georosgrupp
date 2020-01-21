<?php
define('PUBLIC_DIR', dirname(__FILE__));

define('ROOT_DIR', substr(PUBLIC_DIR, 0, strlen(PUBLIC_DIR) - 6));

include_once ROOT_DIR . 'core' . DIRECTORY_SEPARATOR . 'Autoloader.php';

Autoloader::register();

Core::execute();