<?php
/*
================================================================
	Octopus Engine - by Web syndicate && redreem studio 
----------------------------------------------------------------
	http://websyndicate.ru	http://redreem.ru/
----------------------------------------------------------------
	Copyright (c) 2011 redreem studio | captcha.php
================================================================
*/
session_start();
$_SESSION['uid'] = substr(mt_rand(10000,99999), 0, 6); 

define ('ROOT_DIR', dirname(__FILE__));
$img=ImageCreateFromJpeg(ROOT_DIR.'/'.round(mt_rand(1,4)).'.jpg');
$color=ImageColorAllocate($img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
ImageTtfText($img, 23, mt_rand(-5,5), 3, 30, $color, ROOT_DIR.'/'.'addict.ttf',  $_SESSION['uid']);

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: image/jpeg" );

imagejpeg($img, null, 80);
?>