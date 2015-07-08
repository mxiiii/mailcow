<?php

/* Database settings*/
define('MYSQL_HOST','localhost');
define('MYSQL_USER', 'root');
define('MYSQL_PASSWORD', '');
define('MYSQL_DATABASE', 'mailcow');

/* OpenDKIM DNS desc */
define('MAILCOW_OPENDKIM_DNSTXT_FOLDER', '/etc/opendkim/dnstxt');

/* Postfix tables */
define('MAILCOW_ANONYMIZE_HEADERS', '/etc/postfix/mailcow_anonymize_headers.pcre');
define('MAILCOW_REJECT_ATTACHMENTS', '/etc/postfix/mailcow_reject_attachments.regex');
define('MAILCOW_SENDER_ACCESS', '/etc/postfix/mailcow_sender_access');

/* Data files */
define('MC_MBOX_BACKUP', '/var/www/MAILBOX_BACKUP');
define('VT_API_KEY', '/var/www/VT_API_KEY');
define('VT_ENABLE', '/var/www/VT_ENABLE');
define('CAV_ENABLE', '/var/www/CAV_ENABLE');
define('VT_ENABLE_UPLOAD', '/var/www/VT_ENABLE_UPLOAD');

/* Paths */
define('LIB_PATH', 'library/');
define('TEMPLATE_PATH', 'templates/');
define('VENDOR_PATH', LIB_PATH.'vendor/');
define('ASSET_PATH', LIB_PATH.'assets/');
define('CSS_ASSET_PATH', ASSET_PATH.'css/');
define('JS_ASSET_PATH', ASSET_PATH.'js/');
define('IMG_ASSET_PATH', ASSET_PATH.'img/');
define('CONTROLLERS_PATH', LIB_PATH.'controllers/');
define('ABS_PATH', str_replace('\\', '/', realpath(__DIR__.'/../')).'/');

$hostname = exec("/usr/sbin/postconf -h myhostname");
define('HOSTNAME', (!empty($hostname) ? $hostname : '127.0.0.1'));
define('HOSTNAME_0', explode(".", exec("/usr/sbin/postconf -h myhostname"))[0]);
define('HOSTNAME_1', explode(".", exec("/usr/sbin/postconf -h myhostname"))[1]);
define('HOSTNAME_2', explode(".", exec("/usr/sbin/postconf -h myhostname"))[2]);

/* Plugins */
define('SMARTY_VERSION', 'smarty-3.1.25');
define('SMARTY_CLASS', VENDOR_PATH.SMARTY_VERSION.'/libs/Smarty.class.php');
?>