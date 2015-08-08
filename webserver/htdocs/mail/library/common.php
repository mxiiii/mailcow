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
$template->assign('hostname_0', HOSTNAME_0);
$template->assign('hostname_1', HOSTNAME_1);
$template->assign('hostname_2', HOSTNAME_2);
$template->assign('css_asset_path', CSS_ASSET_PATH);
$template->assign('js_asset_path', JS_ASSET_PATH);
$template->assign('img_asset_path', IMG_ASSET_PATH);
$template->assign('mc_mbox_backup', MC_MBOX_BACKUP);


/* CoreClass */
new Core(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
Core::$template = $template;


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

// Standard Routes
Router::addRoute('/', 'get', 'welcome', 'index');
Router::addRoute('/admin', 'get', 'admin', 'index', true);
Router::addRoute('/user', 'get', 'user', 'index');
Router::addRoute('/login', 'get', 'user', 'login');
Router::addRoute('/logout', 'get', 'user', 'logout');

// User GET Routes
Router::addRoute('/do_login', 'post', 'user', 'doLogin');
Router::addRoute('/set_fetch_mail', 'post', 'user', 'set_fetch_mail');
Router::addRoute('/change_password', 'post', 'user', 'change_password');

// Admin POST Routes
Router::addRoute('/save', 'post', 'admin', 'save');
Router::addRoute('/add_domain_admin', 'post', 'admin', 'add_domain_admin');
Router::addRoute('/backup_mail', 'post', 'admin', 'backup_mail');
Router::addRoute('/save_attachments', 'post', 'admin', 'save_attachments');
Router::addRoute('/save_blacklist', 'post', 'admin', 'save_blacklist');
Router::addRoute('/save_privacy', 'post', 'admin', 'save_privacy');
Router::addRoute('/save_dkim', 'post', 'admin', 'save_dkim');
Router::addRoute('/save_message_size', 'post', 'admin', 'save_message_size');

// Mailbox GET Routes
Router::addRoute('/mailbox', 'get', 'mailbox', 'index', true);

Router::addRoute('/add_domain', 'get', 'mailbox', 'add_domain', true);
Router::addRoute('/save_add_domain', 'post', 'mailbox', 'save_add_domain', true);

Router::addRoute('/add_alias', 'get', 'mailbox', 'add_alias', true);
Router::addRoute('/save_add_alias', 'post', 'mailbox', 'save_add_alias', true);

Router::addRoute('/add_domain_alias', 'get', 'mailbox', 'add_domain_alias', true);
Router::addRoute('/save_add_domain_alias', 'post', 'mailbox', 'save_add_domain_alias', true);
?>