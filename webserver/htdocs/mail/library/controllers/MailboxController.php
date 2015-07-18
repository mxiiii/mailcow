<?php
class MailboxController extends BaseController
{
	public function index()
	{
		$domains = Core::$link->query('SELECT domain, aliases, mailboxes, maxquota, quota, active FROM domain WHERE domain IN (SELECT domain from domain_admins WHERE username = \''.$_SESSION['username'].'\') OR \'admin\' = \''.$_SESSION['role'].'\'');
		Core::$template->assign('domains', $domains);

		foreach ($domains as $domain)
		{
			print_r($domain);
		}

		echo '<pre>';
		print_r($domains);
		die();
	}
}
?>