<?php
	$WEB_ROOT = realpath(dirname(__FILE__));
	$SERVER_ROOT = realpath($_SERVER['DOCUMENT_ROOT']);
	//if ($WEB_ROOT === $SERVER_ROOT) 
	//	$WEB_ROOT_PATH = $_SERVER['DOCUMENT_ROOT']."/";
	//else
	//	$WEB_ROOT_PATH = str_replace('\\','/',$_SERVER['DOCUMENT_ROOT'].'/'.substr($WEB_ROOT, strlen($SERVER_ROOT) + 1).'/');
	$WEB_ROOT_PATH = str_replace('\\','/',realpath(dirname(__FILE__)).'/'.substr($WEB_ROOT, strlen($SERVER_ROOT) + 1).'/');
?>