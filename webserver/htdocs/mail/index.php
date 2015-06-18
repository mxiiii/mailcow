<?php
require('library/common.php');

$content = false;

switch($content)
{
	default:
		$content = 'index.tpl';
		break;
}

$template->assign('content', $content);
$template->display('layout.tpl');
?>