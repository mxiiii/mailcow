<?php
class AdminController extends BaseController
{
	public function index()
	{
		if(isset($_SESSION['role']) && $_SESSION['role'] == 'user') header('Location: /user');

		// Username Query
		$username = Core::$link->select('admin', 'username', ['superadmin' => '1'])[0];
		Core::$template->assign('username', $username);

		// Domain Query
		$domains = Core::$link->select('domain', 'domain');
		Core::$template->assign('domains', $domains);

		// Mailbox Query
		$mailboxes = Core::$link->select('mailbox', 'username');
		Core::$template->assign('mailboxes', $mailboxes);
	}

	public function save()
	{	
		header('Location: /admin');
	}
}
?>