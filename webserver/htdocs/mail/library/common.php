<?php
/* Error */
error_reporting(E_ALL);

/* Sessions */
session_start();
session_regenerate_id(true);

/* load configurations */
require('library/config.php');

/* Databaseclass */
require(LIB_PATH.'classes/medoo.min.php');
require(LIB_PATH.'classes/core.class.php');
require(LIB_PATH.'classes/router.class.php');
require(LIB_PATH.'classes/BaseController.class.php');
require(SMARTY_CLASS);

/* Template Settings */
$template = new Smarty;

$template->setTemplateDir('templates/');
$template->setCompileDir('templates_c/');
$template->setConfigDir('configs/');
$template->setCacheDir('cache/');

$template->assign('hostname', HOSTNAME);
$template->assign('css_asset_path', CSS_ASSET_PATH);
$template->assign('js_asset_path', JS_ASSET_PATH);
$template->assign('img_asset_path', IMG_ASSET_PATH);

/* CoreClass */
new Core(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);

Core::$template = $template;

/* Login */
if (isset($_SESSION['mailcow_cc_loggedin']) && !empty($_SESSION['mailcow_cc_loggedin'])) {
	$logged_in_as = $_SESSION['mailcow_cc_username'];
	$logged_in_role = $_SESSION['mailcow_cc_role'];
}
else {
	$logged_in_role = "";
	$logged_in_as = "";
}

/* other settings */
if (isset($_SERVER['SERVER_ADDR']))
{
	$IP = $_SERVER['SERVER_ADDR'];
}
else
{
	$IP = false;
}
if (!filter_var($IP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
{
	$IP = 'YOUR.IP.V.4';
}
elseif (!filter_var($IP, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE))
{
	$IP = 'YOUR.IP.V.4';
}

/* load other Actions */
require(LIB_PATH.'includes/functions.php');
require(LIB_PATH.'includes/trigger_actions.php');


// $_SERVER validation
if(isset($_SERVER['REQUEST_URI']))
{
	$_SERVER['REQUEST_URI'] = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_STRING);
}

/*
 * Fügt eine Route dem System hinzu
 *
 * @param string PATH 			Pfad hinter der URL, wobei / als root gildet
 * @param string POST/GET 		Methode mit auf die Geprüft werden soll
 * @param string CONTROLLER 	Controller der aufgerufen werden soll
 * @param string ACTION 		Action die im Controller aufgerufen werden soll
 *
*/
Router::addRoute('/', 'get', 'welcome', 'index');
?>