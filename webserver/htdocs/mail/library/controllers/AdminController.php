<?php
class AdminController extends BaseController
{
	public function index()
	{
		if(isset($_SESSION['role']) && $_SESSION['role'] == 'user') loc('user');

		// Username Query
		$username = Core::$link->select('admin', 'username', ['superadmin' => '1'])[0];
		Core::$template->assign('username', $username);

		// Domain Admin Query
		$domain_admins = Core::$link->query('SELECT username, LOWER(GROUP_CONCAT(DISTINCT domain SEPARATOR \', \')) AS domain, active FROM domain_admins WHERE username NOT IN (SELECT username FROM admin WHERE superadmin=\'1\') GROUP BY username');
		Core::$template->assign('domain_admins', $domain_admins);

		// Domain Query
		$domains = Core::$link->select('domain', 'domain');
		Core::$template->assign('domains', $domains);

		// Mailbox Query
		$mailboxes = Core::$link->select('mailbox', 'username');
		Core::$template->assign('mailboxes', $mailboxes);
	}

	public function save()
	{
		loc('admin');
	}

	public function add_domain_admin()
	{
		if($_SESSION['role'] !== 'admin') loc('/');
		if(empty($_POST['domain'])) loc('admin', ['warning', 'Bitte wählen Sie eine oder mehr Domains aus.']);

		$username = $_POST['username'];
		$password = $_POST['password'];
		$password2 = $_POST['password2'];
		$active = (isset($_POST['active']) && $_POST['active'] == 'on' ? 1 : 0);

		if(!ctype_alnum(str_replace(array('@', '.', '-'), '', $username)) || empty($username)) loc('admin', ['warning', 'Sie haben einen ungültigen Benutzernamen ausgewählt.']);

		if(!empty($password) && !empty($password2))
		{
			if($password !== $password2) loc('admin', ['warning', 'Die eingegebenen Passwörter stimmen nicht überein.']);
			else
			{
				$password = escapeshellcmd($password);
				exec('/usr/bin/doveadm pw -s SHA512-CRYPT -p '.$password, $hash, $return);
				$password = $hash[0];
				if($return != '0') loc('admin', ['warning', 'Fehler beim erstellen der Passwordhash.']);
				else
				{
					$del_da = Core::$link->delete('domain_admins', ['username' => $username]);
					if($del_da <= 0) loc('admin', ['warning', 'MySQL-Fehler #1.']);

					$del_a = Core::$link->delete('admin', ['username' => $username]);
					if($del_da <= 0) loc('admin', ['warning', 'MySQL-Fehler #2.']);

					foreach($_POST['domain'] AS $domain)
					{
						$insert_da = Core::$link->insert('domain_admins', [
							'username' => $username,
							'domain' => $domain,
							'#created' => 'now()',
							'active' => $active
						]);
						if(!$insert_da) loc('admin', ['warning', 'MySQL-Fehler #3.']);
					}

					$insert_a = Core::$link->insert('admin', [
						'username' => $username,
						'password' => $password,
						'superadmin' => 0,
						'#created' => 'now()',
						'#modified' => 'now()',
						'active' => $active
					]);
					if(!$insert_a) loc('admin', ['warning', 'MySQL-Fehler #3.']);

					loc('admin', ['success', 'Benutzer erfolgreich angelegt.']);
				}
			}
		}
		else
		{
			loc('admin', ['warning', 'Die eingegebenen Passwörter stimmen nicht überein.']);
		}
	}


	public function backup_mail()
	{
		set_mailcow_config('backup', $_POST);
	}

	public function save_attachments()
	{
		if(isset($_POST['vfilter']) && $_POST['vfilter'] == 'filter')
		{
			set_mailcow_config('extlist', $_POST['ext'], 'filter');
		}
		else
		{
			set_mailcow_config('extlist', $_POST['ext'], 'reject');
		}

		if(isset($_POST['virustotalcheckonly']) && $_POST['virustotalcheckonly'] == 'on')
		{
			set_mailcow_config('vtupload', '0');
		}
		else
		{
			set_mailcow_config('vtupload', '1');
		}

		if(isset($_POST['virustotalenable']) && $_POST['virustotalenable'] == 'on')
		{
			set_mailcow_config('vtenable', '1');
		}
		else
		{
			set_mailcow_config('vtenable', '0');
		}

		if(isset($_POST['clamavenable']) && $_POST['clamavenable'] == 'on')
		{
			set_mailcow_config('cavenable', '1');
		}
		else
		{
			set_mailcow_config('cavenable', '0');
		}

		postfix_reload();
	}

	public function save_blacklist()
	{
		set_mailcow_config('senderaccess', $_POST['sender']);
		postfix_reload();
	}

	public function save_privacy()
	{
		if(isset($_POST['anonymize_']))
		{
			if (!isset($_POST['anonymize'])) { $_POST['anonymize'] = ""; }
			set_mailcow_config('anonymize', $_POST['anonymize']);
			postfix_reload();
		}

	}

	public function save_dkim()
	{
		opendkim_table('add', $_POST['dkim_selector'] . '_' . $_POST['dkim_domain']);
	}

	public function save_message_size()
	{
		set_mailcow_config('maxmsgsize', $_POST);
	}
}
?>