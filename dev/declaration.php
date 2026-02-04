<?php
	session_start();
	$WEB_ROOT = realpath(dirname(__FILE__));
	$SERVER_ROOT = realpath($_SERVER['DOCUMENT_ROOT']);
	$WEB_ROOT_PATH = str_replace('\\','/',realpath(dirname(__FILE__)).'/'.substr($WEB_ROOT, strlen($SERVER_ROOT) + 1).'/');
	echo $WEB_ROOT_PATH;
?>