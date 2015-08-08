<?php
class MailboxController extends BaseController
{
	public function index()
	{
		$domains = Core::$link->query('SELECT domain, aliases, mailboxes, maxquota, quota, active FROM domain WHERE domain IN (SELECT domain from domain_admins WHERE username = \''.$_SESSION['username'].'\') OR \'admin\' = \''.$_SESSION['role'].'\'');
		Core::$template->assign('domains', $domains);

		$domain_aliases = Core::$link->query('SELECT alias_domain, target_domain, active FROM alias_domain WHERE target_domain IN (SELECT domain from domain_admins WHERE username = \''.$_SESSION['username'].'\') OR \'admin\' = \''.$_SESSION['role'].'\'');
		Core::$template->assign('domain_aliases', $domain_aliases);

		$mailboxes = Core::$link->query('SELECT mailbox.username, name, active, domain, quota, bytes, messages FROM mailbox, quota2 WHERE (mailbox.username = quota2.username) AND (domain IN (SELECT domain from domain_admins WHERE username = \''.$_SESSION['username'].'\') OR \'admin\' = \''.$_SESSION['role'].'\'');
		Core::$template->assign('mailboxes', $mailboxes);

		$aliases = Core::$link->query('SELECT address, goto, domain, active FROM alias WHERE (address NOT IN (SELECT username FROM mailbox) AND address!=goto) AND (domain IN (SELECT domain from domain_admins WHERE username = \''.$_SESSION['username'].'\') OR \'admin\' = \''.$_SESSION['role'].'\'');
		Core::$template->assign('aliases', $aliases);
	}

	public function add_domain()
	{

	}

	public function save_add_domain()
	{
		extract($_POST);
		if ($_SESSION['role'] != 'admin') loc('mailbox', ['warning', 'Permission denied']);
		if ($maxquota > $quota) loc('add_domain', ['warning', 'Max. size per mailbox can not be greater than domain quota']);

		isset($active) ? $active = '1' : $active = '0';
		isset($backupmx) ? $backupmx = '1' : $backupmx = '0';

		if (!ctype_alnum(str_replace(array('.', '-'), '', $domain))) loc('mailbox', ['warning', 'Domain name invalid']);

		foreach (array($quota, $maxquota, $mailboxes, $aliases) as $data)
		{
			if (!is_numeric($data)) loc('add_domain', ['warning', 'Invalid data type']);
		}

		$result = Core::$link->insert('domain', [
			'domain' => $domain,
			'description' => $description,
			'aliases' => $aliases,
			'mailboxes' => $mailboxes,
			'maxquota' => $maxquota,
			'quota' => $quota,
			'transport' => 'virtual',
			'backupmx' => $backupmx,
			'#created' => 'NOW()',
			'#modified' => 'NOW()',
			'active' => $active,
		]);

		if (!$result) loc('save_add_domain', ['warning', 'MySQL Error.<br>'.Core::$link->last_query()]);
		loc('mailbox', ['success', 'Domain has been successfully added']);
	}

	public function add_alias()
	{

	}

	public function save_add_alias()
	{
		$address = $_POST['address'];
		$goto = $_POST['goto'];
		$active = $_POST['active'];
		$domain = substr($address, strpos($address, '@')+1);

		if(empty($domain)) loc('add_alias', ['warning', 'Domain cannot by empty']);

		if(!Core::$link->query('SELECT domain FROM domain WHERE domain = \''.$domain.'\' AND (domain NOT IN (SELECT domain from domain_admins WHERE username = \''.$_SESSION['username'].'\') OR \'admin\' != \''.$_SESSION['role'].'\')'))
		{
			loc('add_alias', ['warning', 'Permission denied or invalid format']);
		}

		isset($active) ? $active = '1' : $active = '0';

		if((!filter_var($address, FILTER_VALIDATE_EMAIL) && empty($domain)) || !filter_var($goto, FILTER_VALIDATE_EMAIL)) loc('add_alias', ['warning', 'Mail address format invalid']);

		$domain = Core::$link->get('domain', 'domain', ['domain' => $domain]);
		if(!$domain) loc('add_alias', ['warning', 'Domain not found']);

		$destination = Core::$link->get('mailbox', 'username', ['username' => $goto]);
		if(!$destination) loc('add_alias', ['warning', 'Destination address unknown']);

		if(!filter_var($address, FILTER_VALIDATE_EMAIL))
		{
			// $result = Core::$link->insert('alias', [
			// 	'address' => '@'.$domain,
			// 	'goto' => $goto,
			// 	'domain' => $domain,
			// 	'#created' => 'NOW()',
			// 	'#modified' => 'NOW()',
			// 	'active' => $active,
			// ]);

			$result = Core::$link->query('INSERT INTO alias (address, goto, domain, created, modified, active) VALUES (\'@'.$domain.'\', \''.$goto.'\', \''.$domain.'\', NOW(), NOW(), '.$active.')');
		}
		else
		{
			// $result = Core::$link->insert('alias', [
			// 	'address' => $address,
			// 	'goto' => $goto,
			// 	'domain' => $domain,
			// 	'#created' => 'NOW()',
			// 	'#modified' => 'NOW()',
			// 	'active' => $active,
			// ]);

			$result = Core::$link->query('INSERT INTO alias (address, goto, domain, created, modified, active) VALUES (\''.$address.'\', \''.$goto.'\', \''.$domain.'\', NOW(), NOW(), '.$active.')');
		}

		if(!$result) loc('add_alias', ['warning', 'MySQL query failed.<br>'.Core::$link->last_query()]);
		else loc('mailbox', ['success', 'Alias has been successfully added']);
	}

	public function add_domain_alias()
	{
		$target_domains = Core::$link->query('SELECT domain FROM domain WHERE domain IN (SELECT domain from domain_admins WHERE username = \''.$_SESSION['username'].'\') OR \'admin\' = \''.$_SESSION['role'].'\'');
		Core::$template->assign('target_domains', $target_domains);
	}

	public function save_add_domain_alias()
	{

		$alias_domain = $_POST['alias_domain'];
		$target_domain = $_POST['target_domain'];
		$active = $_POST['active'];

		if(empty($alias_domain) || empty($target_domain)) loc('add_domain_alias', ['warning', 'No Domain provided']);

		$result_1 = Core::$link->query('SELECT domain FROM domain WHERE domain = \''.$target_domain.'\' AND (domain NOT IN (SELECT domain from domain_admins WHERE username = \''.$_SESSION['username'].'\) OR \'admin\' != \''.$_SESSION['role'].'\')');
		if(!result_1) loc('add_domain_alias', ['warning', 'Permission denied']);

		isset($active) ? $active = '1' : $active = '0';
		if(!ctype_alnum(str_replace(array('.', '-'), '', $alias_domain)) || empty ($alias_domain)) loc('add_domain_alias', ['warning', 'Alias domain name invalid']);
		if(!ctype_alnum(str_replace(array('.', '-'), '', $target_domain)) || empty ($target_domain)) loc('add_domain_alias', ['warning', 'Target domain name invalid']);

		$domain = Core::$link->get('domain', 'domain', ['domain' => $target_domain]);
		if($domain) loc('add_domain_alias', ['warning', 'Target domain not found']);

		$domain_alias = Core::$link->get('alias_domain', 'alias_domain', ['alias_domain' => $alias_domain]);
		if(!$domain_alias) loc('add_domain_alias', ['warning', 'Alias domain exists']);

		$result = Core::$link->insert('alias_domain', [
			'alias_domain' => $alias_domain,
			'target_domain' => $target_domain,
			'#created' => 'NOW()',
			'#modified' => 'NOW()',
			'active' => $active,
		]);

		if(!$result) loc('add_domain_alias', ['warning', 'MySQL query failed']);
		loc('mailbox', ['success', 'Domain alias has been successfully added']);
	}

	// public function edit_domain_admin()
	// {
	// 	if (empty($_POST['domain']))
	// 	{
	// 		loc('mailbox', ['warning', 'Please assign a domain']);
	// 	}
	// 	if ($_SESSION['mailcow_cc_role'] != "admin")
	// 	{
	// 		loc('mailbox', ['warning', 'Permission denied']);
	// 	}
	// 	array_walk($_POST['domain'], function(&$string) use ($link)
	// 	{
	// 		$string = mysqli_real_escape_string($link, $string);
	// 	});
	// 	$username = mysqli_real_escape_string($link, $_POST['username']);
	// 	if (!ctype_alnum(str_replace(array('@', '.', '-'), '', $username)))
	// 	{
	// 		loc('mailbox', ['warning', 'Invalid username']);
	// 	}
	// 	if (isset($_POST['active']) && $_POST['active'] == "on")
	// 	{
	// 		$active = "1";
	// 	}
	// 	else
	// 	{
	// 		$active = "0";
	// 	}
	// 	$mystring = "DELETE FROM domain_admins WHERE username='$username'";
	// 	if (!mysqli_query($link, $mystring))
	// 	{
	// 		loc('mailbox', ['warning', 'MySQL query failed']);
	// 	}
	// 	foreach ($_POST['domain'] as $domain)
	// 	{
	// 		$mystring = "INSERT INTO domain_admins (username, domain, created, active) VALUES ('$username', '$domain', now(), '$active')";
	// 		if (!mysqli_query($link, $mystring))
	// 		{
	// 			loc('mailbox', ['warning', 'MySQL query failed']);
	// 		}
	// 	}
	// 	$mystring = "UPDATE admin SET modified=now(), active='$active' where username='$username'";
	// 	if (!mysqli_query($link, $mystring))
	// 	{
	// 		loc('mailbox', ['warning', 'MySQL query failed']);
	// 	}
	// 	loc('mailbox', ['success', 'Changes to domain administrator have been saved']);
	// }

	public function add_mailbox()
	{
		$domains = Core::$link->query('SELECT domain FROM domain WHERE domain IN (SELECT domain from domain_admins WHERE username = \''.$_SESSION['username'].'\') OR \'admin\' = \''.$_SESSION['role'].'\'');
		Core::$template->assign('domains', $domains);
	}

	public function save_add_mailbox()
	{
		$password = $_POST['password'];
		$password2 = $_POST['password2'];
		$domain = $_POST['domain'];
		$local_part = $_POST['local_part'];
		$name = $_POST['name'];
		$default_cal = $_POST['default_cal'];
		$default_card = $_POST['default_card'];
		$quota_m = $_POST['quota'];

		// if(empty($password) || empty($password2) || empty($domain) || empty($local_part) || empty($name) || empty($default_cal) || empty($default_card) || empty($quota_m)) loc('add_mailbox', ['warning', 'Not all fields']);

		$quota_b = $quota_m*1048576;
		$maildir = $domain."/".$local_part."/";
		$username = $local_part.'@'.$domain;

		$row_from_domain = Core::$link->get('domain', ['mailboxes', 'maxquota', 'quota'], ['domain' => $domain]);
		$row_from_mailbox_ = Core::$link->query('SELECT count(*) as count, coalesce(round(sum(quota)/1048576), 0) as quota FROM mailbox WHERE domain = \''.$domain.'\'');

		$row_from_mailbox = array();

		foreach($row_from_mailbox_ as $key => $value)
		{
			$row_from_mailbox[$key] = $value;
		}

		$num_mailboxes = $row_from_mailbox[0]['count'];
		$quota_m_in_use = $row_from_mailbox[0]['quota'];
		$num_max_mailboxes = $row_from_domain['mailboxes'];
		$maxquota_m = $row_from_domain['maxquota'];
		$domain_quota_m = $row_from_domain['quota'];

		if(empty($default_cal) || empty($default_card)) loc('add_mailbox', ['warning', 'Calendar and address book cannot be empty']);

		$permission = Core::$link->query('SELECT domain FROM domain WHERE domain = \''.$domain.'\' AND (domain NOT IN (SELECT domain from domain_admins  WHERE username = \''.$_SESSION['username'].'\') OR \'admin\' = \''.$_SESSION['role'].'\')');
		if(!$permission) loc('add_mailbox', ['warning', 'Permission denied']);

		if(!ctype_alnum(str_replace(array('.', '-'), '', $domain)) || empty ($domain)) loc('add_mailbox', ['warning', 'Domain name invalid']);
		if(!ctype_alnum(str_replace(array('.', '-'), '', $local_part) || empty ($local_part))) loc('add_mailbox', ['warning', 'Mailbox alias must be alphanumeric']);
		if(!is_numeric($quota_m)) loc('add_mailbox', ['warning', 'Quota is not numeric']);

		if(!empty($password) && !empty($password2))
		{
			if($password !== $password2) loc('add_mailbox', ['warning', 'Password mismatch']);
			$prep_password = escapeshellcmd($password);
			exec('/usr/bin/doveadm pw -s SHA512-CRYPT -p '.$prep_password.'', $hash, $return);
			$password_sha512c = $hash[0];

			if($return != "0") loc('add_mailbox', ['warning', 'Error creating password hash']);
		}
		else loc('add_mailbox', ['warning', 'Password cannot be empty']);

		if($num_mailboxes >= $num_max_mailboxes) loc('add_mailbox', ['warning', 'Mailbox quota exceeded']);

		$domain = Core::$link->get('domain', 'domain', ['domain' => $domain]);
		if(!$domain) loc('add_mailbox', ['warning', 'Domain not found']);

		if(!filter_var($username, FILTER_VALIDATE_EMAIL)) loc('add_mailbox', ['warning', 'Mail address is invalid']);
		if($quota_m > $maxquota_m) loc('add_mailbox', ['warning', 'Quota over max. quota limit']);
		if(($quota_m_in_use+$quota_m) > $domain_quota_m) loc('add_mailbox', ['warning', 'Quota exceeds quota left']);
		if(isset($_POST['active']) && $_POST['active'] == 'on') $active = 1;
		else $active = 0;

		$result_mailbox = Core::$link->insert('mailbox', [
			'username' => $username,
			'password' => $password_sha512c,
			'name' => $name,
			'maildir' => $maildir,
			'quota' => $quota_b,
			'local_part' => $local_part,
			'domain' => $domain,
			'#created' => 'NOW()',
			'#modified' => 'NOW()',
			'active' => $active
		]);
		if(!$result_mailbox) loc('add_mailbox', ['warning', 'MySQL query failed.<br>'.Core::$link->last_query()]);

		$result_quota = Core::$link->insert('quota2', [
			'username' => $username,
			'bytes' => '',
			'messages' => ''
		]);
		if(!$result_quota) loc('add_mailbox', ['warning', 'MySQL query failed.<br>'.Core::$link->last_query()]);

		$result_alias = Core::$link->insert('alias', [
			'address' => $username,
			'goto' => $username,
			'domain' => $domain,
			'#created' => 'NOW()',
			'#modified' => 'NOW()',
			'active' => $active
		]);
		if(!$result_alias) loc('add_mailbox', ['warning', 'MySQL query failed.<br>'.Core::$link->last_query()]);

		$result_users = Core::$link->query('INSERT INTO users (username, digesta1) VALUES(\''.$username.'\', MD5(CONCAT(\''.$username.'\', \':SabreDAV:\', \''.$password.'\')))');
		if(!$result_users) loc('add_mailbox', ['warning', 'MySQL query failed.<br>'.Core::$link->last_query()]);

		$result_principals = Core::$link->insert('principals', [
			'uri' => 'principals/'.$username,
			'email' => $username,
			'displayname' => $name
		]);
		if(!$result_principals) loc('add_mailbox', ['warning', 'MySQL query failed.<br>'.Core::$link->last_query()]);

		$result_principals_2 = Core::$link->insert('principals', [
			'uri' => 'principals/'.$username.'/calendar-proxy-read',
			'email' => false,
			'displayname' => false
		]);
		if(!$result_principals_2) loc('add_mailbox', ['warning', 'MySQL query failed.<br>'.Core::$link->last_query()]);

		$result_principals_3 = Core::$link->insert('principals', [
			'uri' => 'principals/'.$username.'/calendar-proxy-write',
			'email' => false,
			'displayname' => false
		]);
		if(!$result_principals_3) loc('add_mailbox', ['warning', 'MySQL query failed.<br>'.Core::$link->last_query()]);

		$result_addressbook = Core::$link->insert('addressbooks', [
			'principaluri' => 'principals/'.$username,
			'displayname' => $default_card,
			'uri' => 'default',
			'description' => '',
			'synctoken' => 1
		]);
		if(!$result_addressbook) loc('add_mailbox', ['warning', 'MySQL query failed.<br>'.Core::$link->last_query()]);

		$result_calendars = Core::$link->insert('calendars', [
			'principaluri' => 'principals/'.$username,
			'displayname' => $default_cal,
			'uri' => 'default',
			'description' => '',
			'components' => 'VEVENT,VTODO',
			'synctoken' => 0
		]);
		if(!$result_calendars) loc('add_mailbox', ['warning', 'MySQL query failed.<br>'.Core::$link->last_query()]);

		loc('mailbox', ['success', 'Mailbox has been successfully added']);
	}

	// public function edit_domain()
	// {
	// 	$domain = mysqli_real_escape_string($link, $_POST['domain']);
	// 	$description = mysqli_real_escape_string($link, $_POST['description']);
	// 	$aliases = mysqli_real_escape_string($link, $_POST['aliases']);
	// 	$mailboxes = mysqli_real_escape_string($link, $_POST['mailboxes']);
	// 	$maxquota = mysqli_real_escape_string($link, $_POST['maxquota']);
	// 	$quota = mysqli_real_escape_string($link, $_POST['quota']);

	// 	$row_from_mailbox = mysqli_fetch_assoc(mysqli_query($link, "SELECT count(*) as count, max(coalesce(round(quota/1048576), 0)) as maxquota, coalesce(round(sum(quota)/1048576), 0) as quota FROM mailbox WHERE domain='$domain'"));
	// 	$maxquota_in_use = $row_from_mailbox['maxquota'];
	// 	$domain_quota_m_in_use = $row_from_mailbox['quota'];
	// 	$mailboxes_in_use = $row_from_mailbox['count'];
	// 	$aliases_in_use = mysqli_result(mysqli_query($link, "SELECT count(*) FROM alias WHERE domain='$domain' and address NOT IN (SELECT username FROM mailbox)"));

	// 	global $logged_in_role;
	// 	global $logged_in_as;

	// 	if (!mysqli_result(mysqli_query($link, "SELECT domain FROM domain WHERE domain='$domain' AND (domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
	// 	{
	// 		loc('mailbox', ['warning', 'Permission denied']);
	// 	}
	// 	$numeric_array = array($aliases, $mailboxes, $maxquota, $quota);
	// 	foreach ($numeric_array as $numeric)
	// 	{
	// 		if (!is_numeric($mailboxes))
	// 		{
	// 			loc('mailbox', ['warning', 'Invalid data type']);
	// 		}
	// 	}
	// 	if (!ctype_alnum(str_replace(array('.', '-'), '', $domain)) || empty ($domain))
	// 	{
	// 		loc('mailbox', ['warning', 'Domain name invalid']);
	// 	}
	// 	if ($maxquota > $quota)
	// 	{
	// 		loc('mailbox', ['warning', 'Max. size per mailbox cannot be greater than domain quota.']);
	// 	}
	// 	if ($maxquota_in_use > $maxquota)
	// 	{
	// 		loc('mailbox', ['warning', 'Max. quota per mailbox must be greater than or equal to quota in use.']);
	// 	}
	// 	if ($domain_quota_m_in_use > $quota)
	// 	{
	// 		loc('mailbox', ['warning', 'Max. quota must be greater than or equal to domain quota in use.']);
	// 	}
	// 	if ($mailboxes_in_use > $mailboxes)
	// 	{
	// 		loc('mailbox', ['warning', 'Max. mailboxes must be greater than or equal to mailboxes in use.']);
	// 	}
	// 	if ($aliases_in_use > $aliases)
	// 	{
	// 		loc('mailbox', ['warning', 'Max. aliases must be greater than or equal to aliases in use.']);
	// 	}
	// 	if (isset($_POST['active']) && $_POST['active'] == "on")
	// 	{
	// 		$active = "1";
	// 	}
	// 	else
	// 	{
	// 		$active = "0";
	// 	}
	// 	if (isset($_POST['backupmx']) && $_POST['backupmx'] == "on")
	// 	{
	// 		$backupmx = "1";
	// 	}
	// 	else
	// 	{
	// 		$backupmx = "0";
	// 	}
	// 	$mystring = "UPDATE domain SET modified=now(), backupmx='$backupmx', active='$active', quota='$quota', maxquota='$maxquota', mailboxes='$mailboxes', aliases='$aliases', description='$description' WHERE domain='$domain'";
	// 	if (!mysqli_query($link, $mystring))
	// 	{
	// 		loc('mailbox', ['warning', 'MySQL query failed']);
	// 	}
	// 	loc('mailbox', ['success', 'Changes to domain have been saved']);
	// }

	// public function edit_mailbox()
	// {
	// 	$quota_m = mysqli_real_escape_string($link, $_POST['quota']);
	// 	$quota_b = $quota_m*1048576;
	// 	$username = mysqli_real_escape_string($link, $_POST['username']);
	// 	$name = mysqli_real_escape_string($link, $_POST['name']);
	// 	$password = mysqli_real_escape_string($link, $_POST['password']);
	// 	$password2 = mysqli_real_escape_string($link, $_POST['password2']);
	// 	if (!is_numeric($quota_m))
	// 	{
	// 		loc('mailbox', ['warning', 'Quota must be numeric']);
	// 	}
	// 	if (!ctype_alnum(str_replace(array('@', '.', '-'), '', $username)))
	// 	{
	// 		loc('mailbox', ['warning', 'Invalid username']);
	// 	}
	// 	$domain = mysqli_result(mysqli_query($link, "SELECT domain FROM mailbox WHERE username='$username'"));
	// 	$quota_m_now = mysqli_result(mysqli_query($link, "SELECT coalesce(round(sum(quota)/1048576), 0) as quota FROM mailbox WHERE username='$username'"));
	// 	$quota_m_in_use = mysqli_result(mysqli_query($link, "SELECT coalesce(round(sum(quota)/1048576), 0) as quota FROM mailbox WHERE domain='$domain'"));
	// 	$row_from_domain = mysqli_fetch_assoc(mysqli_query($link, "SELECT quota, maxquota FROM domain WHERE domain='$domain'"));
	// 	$maxquota_m = $row_from_domain['maxquota'];
	// 	$domain_quota_m = $row_from_domain['quota'];
	// 	global $logged_in_role;
	// 	global $logged_in_as;
	// 	if (!mysqli_result(mysqli_query($link, "SELECT domain FROM domain WHERE domain='$domain' AND (domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
	// 	{
	// 		loc('mailbox', ['warning', 'Permission denied']);
	// 	}
	// 	if ($quota_m > $maxquota_m)
	// 	{
	// 		loc('mailbox', ['warning', 'Quota over max. quota limit']);
	// 	}
	// 	if (($quota_m_in_use-$quota_m_now+$quota_m) > $domain_quota_m)
	// 	{
	// 		loc('mailbox', ['warning', 'Quota exceeds quota left']);
	// 	}
	// 	if (isset($_POST['active']) && $_POST['active'] == "on")
	// 	{
	// 		$active = "1";
	// 	}
	// 	else
	// 	{
	// 		$active = "0";
	// 	}
	// 	if (!empty($password) && !empty($password2))
	// 	{
	// 		if ($password != $password2)
	// 		{
	// 			loc('mailbox', ['warning', 'Password mismatch']);
	// 		}
	// 		$prep_password = escapeshellcmd($password);
	// 		exec("/usr/bin/doveadm pw -s SHA512-CRYPT -p $prep_password", $hash, $return);
	// 		$password_sha512c = $hash[0];
	// 		if ($return != "0")
	// 		{
	// 			loc('mailbox', ['warning', 'Error creating password hash']);
	// 		}
	// 		$update_user = "UPDATE mailbox SET modified=now(), active='$active', password='$password_sha512c', name='$name', quota='$quota_b' WHERE username='$username';";
	// 		$update_user .= "UPDATE users SET digesta1=MD5(CONCAT('$username', ':SabreDAV:', '$password')) WHERE username='$username';";
	// 		if (!mysqli_multi_query($link, $update_user))
	// 		{
	// 			loc('mailbox', ['warning', 'MySQL query failed']);
	// 		}
	// 		while ($link->next_result())
	// 		{
	// 			if (!$link->more_results()) break;
	// 		}
	// 		loc('mailbox', ['success', 'Changes to mailbox have been saved']);
	// 	}
	// 	$mystring = "UPDATE mailbox SET modified=now(), active='$active', name='$name', quota='$quota_b' WHERE username='$username'";
	// 	if (!mysqli_query($link, $mystring))
	// 	{
	// 		loc('mailbox', ['warning', 'MySQL query failed']);
	// 	}
	// 	else
	// 	{
	// 		loc('mailbox', ['success', 'Changes to mailbox have been saved']);
	// 	}
	// }

	// public function delete_domain()
	// {
	// 	$domain = mysqli_real_escape_string($link, $_POST['domain']);
	// 	if ($_SESSION['mailcow_cc_role'] != "admin")
	// 	{
	// 		loc('mailbox', ['warning', 'Permission denied']);
	// 	}
	// 	if (!ctype_alnum(str_replace(array('.', '-'), '', $domain)) || empty ($domain))
	// 	{
	// 		loc('mailbox', ['warning', 'Domain name invalid']);
	// 	}
	// 	$mystring = "SELECT username FROM mailbox WHERE domain='$domain';";
	// 	if (!mysqli_query($link, $mystring) || !empty(mysqli_result(mysqli_query($link, $mystring))))
	// 	{
	// 		loc('mailbox', ['warning', 'Domain is not empty! Please delete mailboxes first.']);
	// 	}
	// 	foreach (array("domain", "alias", "domain_admins") as $deletefrom)
	// 	{
	// 		$mystring = "DELETE FROM $deletefrom WHERE domain='$domain'";
	// 		if (!mysqli_query($link, $mystring))
	// 		{
	// 			loc('mailbox', ['warning', 'MySQL query failed']);
	// 		}
	// 	}
	// 	$mystring = "DELETE FROM alias_domain WHERE target_domain='$domain'";
	// 	if (!mysqli_query($link, $mystring))
	// 	{
	// 		loc('mailbox', ['warning', 'MySQL query failed']);
	// 	}
	// 	loc('mailbox', ['success', 'Domain was successfully deleted']);
	// }

	// public function delete_alias()
	// {
	// 	$address = mysqli_real_escape_string($link, $_POST['address']);
	// 	global $logged_in_role;
	// 	global $logged_in_as;
	// 	if (!mysqli_result(mysqli_query($link, "SELECT domain FROM alias WHERE address='$address' AND (domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
	// 	{
	// 		loc('mailbox', ['warning', 'Permission denied']);
	// 	}
	// 	if (!ctype_alnum(str_replace(array('@', '.', '-'), '', $address)))
	// 	{
	// 		loc('mailbox', ['warning', 'Mail address invalid']);
	// 	}
	// 	$mystring = "DELETE FROM alias WHERE address='$address' AND address NOT IN (SELECT username FROM mailbox)";
	// 	if (!mysqli_query($link, $mystring))
	// 	{
	// 		loc('mailbox', ['warning', 'MySQL query failed']);
	// 	}
	// 	loc('mailbox', ['success', 'Alias was successfully deleted']);
	// }

	// public function delete_domain_admin()
	// {
	// 	if ($_SESSION['mailcow_cc_role'] != "admin")
	// 	{
	// 		loc('mailbox', ['warning', 'Permission denied']);
	// 	}
	// 	$username = mysqli_real_escape_string($link, $_POST['username']);
	// 	if (!ctype_alnum(str_replace(array('@', '.', '-'), '', $username)))
	// 	{
	// 		loc('mailbox', ['warning', 'Invalid username']);
	// 	}
	// 	$delete_domain = "DELETE FROM domain_admins WHERE username='$username';";
	// 	$delete_domain .= "DELETE FROM admin WHERE username='$username';";
	// 	if (!mysqli_multi_query($link, $delete_domain))
	// 	{
	// 		loc('mailbox', ['warning', 'MySQL query failed']);
	// 	}
	// 	while ($link->next_result())
	// 	{
	// 		if (!$link->more_results()) break;
	// 	}
	// 	loc('mailbox', ['success', 'Domain administrator was successfully deleted']);
	// }

	// public function delete_alias_domain()
	// {
	// 	$alias_domain = mysqli_real_escape_string($link, $_POST['alias_domain']);
	// 	global $logged_in_role;
	// 	global $logged_in_as;
	// 	if (!mysqli_result(mysqli_query($link, "SELECT target_domain FROM alias_domain WHERE alias_domain='$alias_domain' AND (target_domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
	// 	{
	// 		loc('mailbox', ['warning', 'Permission denied']);
	// 	}
	// 	if (!ctype_alnum(str_replace(array('.', '-'), '', $alias_domain)))
	// 	{
	// 		loc('mailbox', ['warning', 'Domain name invalid']);
	// 	}
	// 	$mystring = "DELETE FROM alias_domain WHERE alias_domain='$alias_domain'";
	// 	if (!mysqli_query($link, $mystring))
	// 	{
	// 		loc('mailbox', ['warning', 'MySQL query failed']);
	// 	}
	// 	loc('mailbox', ['success', 'Alias domain was successfully deleted']);
	// }

	// public function delete_mailbox()
	// {
	// 	$username = mysqli_real_escape_string($link, $_POST['username']);
	// 	global $logged_in_role;
	// 	global $logged_in_as;
	// 	if (!mysqli_result(mysqli_query($link, "SELECT domain FROM mailbox WHERE username='$username' AND (domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
	// 	{
	// 		loc('mailbox', ['warning', 'Permission denied']);
	// 	}
	// 	if (!filter_var($username, FILTER_VALIDATE_EMAIL))
	// 	{
	// 		loc('mailbox', ['warning', 'Mail address invalid']);
	// 	}
	// 	$delete_user = "DELETE FROM alias WHERE goto='$username';";
	// 	$delete_user .= "DELETE FROM quota2 WHERE username='$username';";
	// 	$delete_user .= "DELETE FROM calendarobjects WHERE calendarid IN (SELECT id from calendars where principaluri='principals/$username');";
	// 	$delete_user .= "DELETE FROM cards WHERE addressbookid IN (SELECT id from calendars where principaluri='principals/$username');";
	// 	$delete_user .= "DELETE FROM mailbox WHERE username='$username';";
	// 	$delete_user .= "DELETE FROM users WHERE username='$username';";
	// 	$delete_user .= "DELETE FROM principals WHERE uri='principals/$username';";
	// 	$delete_user .= "DELETE FROM principals WHERE uri='principals/$username/calendar-proxy-read';";
	// 	$delete_user .= "DELETE FROM principals WHERE uri='principals/$username/calendar-proxy-write';";
	// 	$delete_user .= "DELETE FROM addressbooks WHERE principaluri='principals/$username';";
	// 	$delete_user .= "DELETE FROM calendars WHERE principaluri='principals/$username';";
	// 	if (!mysqli_multi_query($link, $delete_user))
	// 	{
	// 		loc('mailbox', ['warning', 'MySQL query failed']);
	// 	}
	// 	while ($link->next_result())
	// 	{
	// 		if (!$link->more_results()) break;
	// 	}
	// 	loc('mailbox', ['success', 'Mailbox was successfully deleted']);
	// }
}
?>