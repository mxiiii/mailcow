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

/* Database Setup */
$db = new medoo([
	'database_type' => 'mysql',
	'database_name' => MYSQL_DATABASE,
	'server' => MYSQL_HOST,
	'username' => MYSQL_USER,
	'password' => MYSQL_PASSWORD,
	'charset' => 'utf8'
]);

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
?>