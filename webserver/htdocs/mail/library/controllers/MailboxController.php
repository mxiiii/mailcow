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
	public function add_domain()
	{
		extract($postarray);
		if ($_SESSION['mailcow_cc_role'] != 'admin')
		{
			header("Location: do.php?event=".base64_encode("Permission denied"));
			die("Permission denied");
		}
		if ($maxquota > $quota)
		{
			header("Location: do.php?event=".base64_encode("Max. size per mailbox can not be greater than domain quota"));
			die("Max. size per mailbox can not be greater than domain quota");
		}
		isset($active) ? $active = '1' : $active = '0';
		isset($backupmx) ? $backupmx = '1' : $backupmx = '0';
		if (!ctype_alnum(str_replace(array('.', '-'), '', $domain)))
		{
			header("Location: do.php?event=".base64_encode("Domain name invalid"));
			die('Domain name invalid');
		}
		foreach (array($quota, $maxquota, $mailboxes, $aliases) as $data)
		{
			if (!is_numeric($data))
			{
				header('Location: do.php?event='.base64_encode(''.$data.' is not numeric'));
				die($data.' is not numeric');
			}
		}
		$link->insert('domain', [
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
		if (!empty($link->error()[2]))
		{
			header('Location: do.php?event='.base64_encode($link->error()[2]));
			die($link->error()[2]);
		}
		header('Location: do.php?return=success');
	}
	public function add_alias()
	{
		$address = mysqli_real_escape_string($link, $_POST['address']);
		$goto = mysqli_real_escape_string($link, $_POST['goto']);
		$domain = substr($address, strpos($address, '@')+1);
		global $logged_in_role;
		global $logged_in_as;
		if (empty($domain))
		{
			header("Location: do.php?event=".base64_encode("Domain cannot be empty"));
			die("Domain cannot by empty");
		}
		if (!mysqli_result(mysqli_query($link, "SELECT domain FROM domain WHERE domain='$domain' AND (domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
		{
			header("Location: do.php?event=".base64_encode("Permission denied or invalid format"));
			die("Permission denied or invalid format");
		}
		if (isset($_POST['active']) && $_POST['active'] == "on")
		{
			$active = "1";
		}
		else
		{
			$active = "0";
		}
		if ((!filter_var($address, FILTER_VALIDATE_EMAIL) && empty($domain)) || !filter_var($goto, FILTER_VALIDATE_EMAIL))
		{
			header("Location: do.php?event=".base64_encode("Mail address format invalid"));
			die("Mail address format invalid");
		}
		if (!mysqli_result(mysqli_query($link, "SELECT domain FROM domain WHERE domain='$domain'")))
		{
			header("Location: do.php?event=".base64_encode("Domain $domain not found"));
			die("Domain $domain not found");
		}
		if (!mysqli_result(mysqli_query($link, "SELECT username FROM mailbox WHERE username='$goto'")))
		{
			header("Location: do.php?event=".base64_encode("Destination address unknown"));
			die("Destination address unknown");
		}
		if (!filter_var($address, FILTER_VALIDATE_EMAIL))
		{
			$mystring = "INSERT INTO alias (address, goto, domain, created, modified, active) VALUE ('@$domain', '$goto', '$domain', now(), now(), '$active')";
		}
		else
		{
			$mystring = "INSERT INTO alias (address, goto, domain, created, modified, active) VALUE ('$address', '$goto', '$domain', now(), now(), '$active')";
		}
		if (!mysqli_query($link, $mystring))
		{
			header("Location: do.php?event=".base64_encode("MySQL query failed"));
			die("MySQL query failed");
		}
		header('Location: do.php?return=success');
	}
	public function add_domain_alias()
	{
		$alias_domain = mysqli_real_escape_string($link, $_POST['alias_domain']);
		$target_domain = mysqli_real_escape_string($link, $_POST['target_domain']);
		global $logged_in_role;
		global $logged_in_as;
		if (!mysqli_result(mysqli_query($link, "SELECT domain FROM domain WHERE domain='$target_domain' AND (domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
		{
			header("Location: do.php?event=".base64_encode("Permission denied"));
			die("Permission denied");
		}
		if (isset($_POST['active']) && $_POST['active'] == "on")
		{
			$active = "1";
		}
		else
		{
			$active = "0";
		}
		if (!ctype_alnum(str_replace(array('.', '-'), '', $alias_domain)) || empty ($alias_domain))
		{
			header("Location: do.php?event=".base64_encode("Alias domain name invalid"));
			die("Alias domain name invalid");
		}
		if (!ctype_alnum(str_replace(array('.', '-'), '', $target_domain)) || empty ($target_domain))
		{
			header("Location: do.php?event=".base64_encode("Target domain name invalid"));
			die("Target domain name invalid");
		}
		if (!mysqli_result(mysqli_query($link, "SELECT domain FROM domain where domain='$target_domain'")))
		{
			header("Location: do.php?event=".base64_encode("Target domain $target_domain not found"));
			die("Target domain $target_domain not found");
		}
		if (mysqli_result(mysqli_query($link, "SELECT alias_domain FROM alias_domain where alias_domain='$alias_domain'")))
		{
			header("Location: do.php?event=".base64_encode("Alias domain exists"));
			die("Alias domain exists");
		}
		$mystring = "INSERT INTO alias_domain (alias_domain, target_domain, created, modified, active) VALUE ('$alias_domain', '$target_domain', now(), now(), '$active')";
		if (!mysqli_query($link, $mystring))
		{
			header("Location: do.php?event=".base64_encode("MySQL query failed"));
			die("MySQL query failed");
		}
		header('Location: do.php?return=success');
	}
	public function edit_domain_admin()
	{
		if (empty($_POST['domain']))
		{
			header("Location: do.php?event=".base64_encode("Please assign a domain"));
			die("Please assign a domain");
		}
		if ($_SESSION['mailcow_cc_role'] != "admin")
		{
			header("Location: do.php?event=".base64_encode("Permission denied"));
			die("Permission denied");
		}
		array_walk($_POST['domain'], function(&$string) use ($link)
		{
			$string = mysqli_real_escape_string($link, $string);
		});
		$username = mysqli_real_escape_string($link, $_POST['username']);
		if (!ctype_alnum(str_replace(array('@', '.', '-'), '', $username)))
		{
			header("Location: do.php?event=".base64_encode("Invalid username"));
			die("Invalid username");
		}
		if (isset($_POST['active']) && $_POST['active'] == "on")
		{
			$active = "1";
		}
		else
		{
			$active = "0";
		}
		$mystring = "DELETE FROM domain_admins WHERE username='$username'";
		if (!mysqli_query($link, $mystring))
		{
			header("Location: do.php?event=".base64_encode("MySQL query failed"));
			die("MySQL query failed");
		}
		foreach ($_POST['domain'] as $domain)
		{
			$mystring = "INSERT INTO domain_admins (username, domain, created, active) VALUES ('$username', '$domain', now(), '$active')";
			if (!mysqli_query($link, $mystring))
			{
				header("Location: do.php?event=".base64_encode("MySQL query failed"));
				die("MySQL query failed");
			}
		}
		$mystring = "UPDATE admin SET modified=now(), active='$active' where username='$username'";
		if (!mysqli_query($link, $mystring))
		{
			header("Location: do.php?event=".base64_encode("MySQL query failed"));
			die("MySQL query failed");
		}
		header('Location: do.php?return=success');
	}
	public function add_mailbox()
	{
		$password = mysqli_real_escape_string($link, $_POST['password']);
		$password2 = mysqli_real_escape_string($link, $_POST['password2']);
		$domain = mysqli_real_escape_string($link, $_POST['domain']);
		$local_part = mysqli_real_escape_string($link, $_POST['local_part']);
		$name = mysqli_real_escape_string($link, $_POST['name']);
		$default_cal = mysqli_real_escape_string($link, $_POST['default_cal']);
		$default_card = mysqli_real_escape_string($link, $_POST['default_card']);
		$quota_m = mysqli_real_escape_string($link, $_POST['quota']);

		$quota_b = $quota_m*1048576;
		$maildir = $domain."/".$local_part."/";
		$username = $local_part.'@'.$domain;

		$row_from_domain = mysqli_fetch_assoc(mysqli_query($link, "SELECT mailboxes, maxquota, quota FROM domain WHERE domain='$domain'"));
		$row_from_mailbox = mysqli_fetch_assoc(mysqli_query($link, "SELECT count(*) as count, coalesce(round(sum(quota)/1048576), 0) as quota FROM mailbox WHERE domain='$domain'"));

		$num_mailboxes = $row_from_mailbox['count'];
		$quota_m_in_use = $row_from_mailbox['quota'];
		$num_max_mailboxes = $row_from_domain['mailboxes'];
		$maxquota_m = $row_from_domain['maxquota'];
		$domain_quota_m = $row_from_domain['quota'];

		global $logged_in_role;
		global $logged_in_as;

		if (empty($default_cal) || empty($default_card))
		{
			header("Location: do.php?event=".base64_encode("Calendar and address book cannot be empty"));
			die("Calendar and address book cannot be empty");
		}

		if (!mysqli_result(mysqli_query($link, "SELECT domain FROM domain WHERE domain='$domain' AND (domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
		{
			header("Location: do.php?event=".base64_encode("Permission denied"));
			die("Permission denied");
		}
		if (!ctype_alnum(str_replace(array('.', '-'), '', $domain)) || empty ($domain))
		{
			header("Location: do.php?event=".base64_encode("Domain name invalid"));
			die("Domain name invalid");
		}
		if (!ctype_alnum(str_replace(array('.', '-'), '', $local_part) || empty ($local_part)))
		{
			header("Location: do.php?event=".base64_encode("Mailbox alias must be alphanumeric"));
			die("Mailbox alias must be alphanumeric");
		}
		if (!is_numeric($quota_m))
		{
			header("Location: do.php?event=".base64_encode("Quota is not numeric"));
			die("Quota is not numeric");
		}
		if (!empty($password) && !empty($password2))
		{
			if ($password != $password2)
			{
				header("Location: do.php?event=".base64_encode("Password mismatch"));
				die("Password mismatch");
			}
			$prep_password = escapeshellcmd($password);
			exec("/usr/bin/doveadm pw -s SHA512-CRYPT -p $prep_password", $hash, $return);
			$password_sha512c = $hash[0];
			if ($return != "0")
			{
				header("Location: do.php?event=".base64_encode("Error creating password hash"));
				die("Error creating password hash");
			}
		}
		else
		{
			header("Location: do.php?event=".base64_encode("Password cannot be empty"));
			die("Password cannot be empty");
		}
		if ($num_mailboxes >= $num_max_mailboxes)
		{
			header("Location: do.php?event=".base64_encode("Mailbox quota exceeded ($num_mailboxes of $num_max_mailboxes)"));
			die("Mailbox quota exceeded ($num_mailboxes of $num_max_mailboxes)");
		}
		if (!mysqli_result(mysqli_query($link, "SELECT domain FROM domain where domain='$domain'")))
		{
			header("Location: do.php?event=".base64_encode("Domain $domain not found"));
			die("Domain $domain not found");
		}
		if (!filter_var($username, FILTER_VALIDATE_EMAIL))
		{
			header("Location: do.php?event=".base64_encode("Mail address is invalid"));
			die("Mail address is invalid");
		}
		if ($quota_m > $maxquota_m)
		{
			header("Location: do.php?event=".base64_encode("Quota over max. quota limit ($maxquota_m M)"));
			die("Quota over max. quota limit ($maxquota_m M)");
		}
		if (($quota_m_in_use+$quota_m) > $domain_quota_m)
		{
			$quota_left_m = ($domain_quota_m - $quota_m_in_use);
			header("Location: do.php?event=".base64_encode("Quota exceeds quota left ($quota_left_m M)"));
			die("Quota exceeds quota left ($quota_left_m M)");
		}
		if (isset($_POST['active']) && $_POST['active'] == "on")
		{
			$active = "1";
		}
		else
		{
			$active = "0";
		}
		$create_user = "INSERT INTO mailbox (username, password, name, maildir, quota, local_part, domain, created, modified, active)
				VALUES ('$username', '$password_sha512c', '$name', '$maildir', '$quota_b', '$local_part', '$domain', now(), now(), '$active');";
		$create_user .= "INSERT INTO quota2 (username, bytes, messages)
				VALUES ('$username', '', '');";
		$create_user .= "INSERT INTO alias (address, goto, domain, created, modified, active)
				VALUES ('$username', '$username', '$domain', now(), now(), '$active');";
		$create_user .= "INSERT INTO users (username, digesta1)
				VALUES('$username', MD5(CONCAT('$username', ':SabreDAV:', '$password')));";
		$create_user .= "INSERT INTO principals (uri,email,displayname)
				VALUES ('principals/$username', '$username', '$name');";
		$create_user .= "INSERT INTO principals (uri,email,displayname)
				VALUES ('principals/$username/calendar-proxy-read', null, null);";
		$create_user .= "INSERT INTO principals (uri,email,displayname)
				VALUES ('principals/$username/calendar-proxy-write', null, null);";
		$create_user .= "INSERT INTO addressbooks (principaluri, displayname, uri, description, synctoken)
				VALUES ('principals/$username','$default_card','default','','1');";
		$create_user .= "INSERT INTO calendars (principaluri, displayname, uri, description, components, transparent)
				VALUES ('principals/$username','$default_cal','default','','VEVENT,VTODO', '0');";
		if (!mysqli_multi_query($link, $create_user))
		{
			header("Location: do.php?event=".base64_encode("MySQL query failed"));
			die("MySQL query failed");
		}
		while ($link->next_result())
		{
			if (!$link->more_results()) break;
		}
		header('Location: do.php?return=success');
	}
	public function edit_domain()
	{
		$domain = mysqli_real_escape_string($link, $_POST['domain']);
		$description = mysqli_real_escape_string($link, $_POST['description']);
		$aliases = mysqli_real_escape_string($link, $_POST['aliases']);
		$mailboxes = mysqli_real_escape_string($link, $_POST['mailboxes']);
		$maxquota = mysqli_real_escape_string($link, $_POST['maxquota']);
		$quota = mysqli_real_escape_string($link, $_POST['quota']);

		$row_from_mailbox = mysqli_fetch_assoc(mysqli_query($link, "SELECT count(*) as count, max(coalesce(round(quota/1048576), 0)) as maxquota, coalesce(round(sum(quota)/1048576), 0) as quota FROM mailbox WHERE domain='$domain'"));
		$maxquota_in_use = $row_from_mailbox['maxquota'];
		$domain_quota_m_in_use = $row_from_mailbox['quota'];
		$mailboxes_in_use = $row_from_mailbox['count'];
		$aliases_in_use = mysqli_result(mysqli_query($link, "SELECT count(*) FROM alias WHERE domain='$domain' and address NOT IN (SELECT username FROM mailbox)"));

		global $logged_in_role;
		global $logged_in_as;

		if (!mysqli_result(mysqli_query($link, "SELECT domain FROM domain WHERE domain='$domain' AND (domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
		{
			header("Location: do.php?event=".base64_encode("Permission denied"));
			die("Permission denied");
		}
		$numeric_array = array($aliases, $mailboxes, $maxquota, $quota);
		foreach ($numeric_array as $numeric)
		{
			if (!is_numeric($mailboxes))
			{
				header("Location: do.php?event=".base64_encode("$numeric must be numeric"));
				die("$numeric must be numeric");
			}
		}
		if (!ctype_alnum(str_replace(array('.', '-'), '', $domain)) || empty ($domain))
		{
			header("Location: do.php?event=".base64_encode("Domain name invalid"));
			die("Domain name invalid");
		}
		if ($maxquota > $quota)
		{
			header("Location: do.php?event=".base64_encode("Max. size per mailbox can not be greater than domain quota"));
			die("Max. size per mailbox can not be greater than domain quota");
		}
		if ($maxquota_in_use > $maxquota)
		{
			header("Location: do.php?event=".base64_encode("Max. size per mailbox must be greater than or equal to $maxquota_in_use"));
			die("Max. quota per mailbox must be greater than or equal to $maxquota_in_use");
		}
		if ($domain_quota_m_in_use > $quota)
		{
			header("Location: do.php?event=".base64_encode("Domain quota must be greater than or equal to $domain_quota_m_in_use"));
			die("Max. quota must be greater than or equal to $domain_quota_m_in_use");
		}
		if ($mailboxes_in_use > $mailboxes)
		{
			header("Location: do.php?event=".base64_encode("Max. mailboxes must be greater than or equal to $mailboxes_in_use"));
			die("Max. mailboxes must be greater than or equal to $mailboxes_in_use");
		}
		if ($aliases_in_use > $aliases)
		{
			header("Location: do.php?event=".base64_encode("Max. aliases must be greater than or equal to $aliases_in_use"));
			die("Max. aliases must be greater than or equal to $aliases_in_use");
		}
		if (isset($_POST['active']) && $_POST['active'] == "on")
		{
			$active = "1";
		}
		else
		{
			$active = "0";
		}
		if (isset($_POST['backupmx']) && $_POST['backupmx'] == "on")
		{
			$backupmx = "1";
		}
		else
		{
			$backupmx = "0";
		}
		$mystring = "UPDATE domain SET modified=now(), backupmx='$backupmx', active='$active', quota='$quota', maxquota='$maxquota', mailboxes='$mailboxes', aliases='$aliases', description='$description' WHERE domain='$domain'";
		if (!mysqli_query($link, $mystring))
		{
			header("Location: do.php?event=".base64_encode("MySQL query failed"));
			die("MySQL query failed");
		}
		header('Location: do.php?return=success');
	}
	public function edit_mailbox()
	{
		$quota_m = mysqli_real_escape_string($link, $_POST['quota']);
		$quota_b = $quota_m*1048576;
		$username = mysqli_real_escape_string($link, $_POST['username']);
		$name = mysqli_real_escape_string($link, $_POST['name']);
		$password = mysqli_real_escape_string($link, $_POST['password']);
		$password2 = mysqli_real_escape_string($link, $_POST['password2']);
		if (!is_numeric($quota_m))
		{
			header("Location: do.php?event=".base64_encode("Quota not numeric"));
			die("Quota not numeric");
		}
		if (!ctype_alnum(str_replace(array('@', '.', '-'), '', $username)))
		{
			header("Location: do.php?event=".base64_encode("Invalid username"));
			die("Invalid username");
		}
		$domain = mysqli_result(mysqli_query($link, "SELECT domain FROM mailbox WHERE username='$username'"));
		$quota_m_now = mysqli_result(mysqli_query($link, "SELECT coalesce(round(sum(quota)/1048576), 0) as quota FROM mailbox WHERE username='$username'"));
		$quota_m_in_use = mysqli_result(mysqli_query($link, "SELECT coalesce(round(sum(quota)/1048576), 0) as quota FROM mailbox WHERE domain='$domain'"));
		$row_from_domain = mysqli_fetch_assoc(mysqli_query($link, "SELECT quota, maxquota FROM domain WHERE domain='$domain'"));
		$maxquota_m = $row_from_domain['maxquota'];
		$domain_quota_m = $row_from_domain['quota'];
		global $logged_in_role;
		global $logged_in_as;
		if (!mysqli_result(mysqli_query($link, "SELECT domain FROM domain WHERE domain='$domain' AND (domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
		{
			header("Location: do.php?event=".base64_encode("Permission denied"));
			die("Permission denied");
		}
		if ($quota_m > $maxquota_m)
		{
			header("Location: do.php?event=".base64_encode("Quota over max. quota limit ($maxquota_m M)"));
			die("Quota over max. quota limit ($maxquota_m M)");
		}
		if (($quota_m_in_use-$quota_m_now+$quota_m) > $domain_quota_m)
		{
			$quota_left_m = ($domain_quota_m - $quota_m_in_use + $quota_m_now);
			header("Location: do.php?event=".base64_encode("Quota exceeds quota left (max. $quota_left_m M)"));
			die("Quota exceeds quota left (max. $quota_left_m M)");
		}
		if (isset($_POST['active']) && $_POST['active'] == "on")
		{
			$active = "1";
		}
		else
		{
			$active = "0";
		}
		if (!empty($password) && !empty($password2))
		{
			if ($password != $password2)
			{
				header("Location: do.php?event=".base64_encode("Password mismatch"));
				die("Password mismatch");
			}
			$prep_password = escapeshellcmd($password);
			exec("/usr/bin/doveadm pw -s SHA512-CRYPT -p $prep_password", $hash, $return);
			$password_sha512c = $hash[0];
			if ($return != "0")
			{
				header("Location: do.php?event=".base64_encode("Error creating password hash"));
				die("Error creating password hash");
			}
			$update_user = "UPDATE mailbox SET modified=now(), active='$active', password='$password_sha512c', name='$name', quota='$quota_b' WHERE username='$username';";
			$update_user .= "UPDATE users SET digesta1=MD5(CONCAT('$username', ':SabreDAV:', '$password')) WHERE username='$username';";
			if (!mysqli_multi_query($link, $update_user))
			{
				header("Location: do.php?event=".base64_encode("MySQL query failed"));
				die("MySQL query failed");
			}
			while ($link->next_result())
			{
				if (!$link->more_results()) break;
			}
			header('Location: do.php?return=success');
		}
		$mystring = "UPDATE mailbox SET modified=now(), active='$active', name='$name', quota='$quota_b' WHERE username='$username'";
		if (!mysqli_query($link, $mystring))
		{
			header("Location: do.php?event=".base64_encode("MySQL query failed"));
			die("MySQL query failed");
		}
		else
		{
			header('Location: do.php?return=success');
		}
	}
	public function delete_domain()
	{
		$domain = mysqli_real_escape_string($link, $_POST['domain']);
		if ($_SESSION['mailcow_cc_role'] != "admin")
		{
			header("Location: do.php?event=".base64_encode("Permission denied"));
			die("Permission denied");
		}
		if (!ctype_alnum(str_replace(array('.', '-'), '', $domain)) || empty ($domain))
		{
			header("Location: do.php?event=".base64_encode("Domain name invalid"));
			die("Domain name invalid");
		}
		$mystring = "SELECT username FROM mailbox WHERE domain='$domain';";
		if (!mysqli_query($link, $mystring) || !empty(mysqli_result(mysqli_query($link, $mystring))))
		{
			header("Location: do.php?event=".base64_encode("Domain is not empty! Please delete mailboxes first."));
			die("Domain is not empty! Please delete mailboxes first.");
		}
		foreach (array("domain", "alias", "domain_admins") as $deletefrom)
		{
			$mystring = "DELETE FROM $deletefrom WHERE domain='$domain'";
			if (!mysqli_query($link, $mystring))
			{
				header("Location: do.php?event=".base64_encode("MySQL query failed"));
				die("MySQL query failed");
			}
		}
		$mystring = "DELETE FROM alias_domain WHERE target_domain='$domain'";
		if (!mysqli_query($link, $mystring))
		{
			header("Location: do.php?event=".base64_encode("MySQL query failed"));
			die("MySQL query failed");
		}
		header('Location: do.php?return=success');
	}
	public function delete_alias()
	{
		$address = mysqli_real_escape_string($link, $_POST['address']);
		global $logged_in_role;
		global $logged_in_as;
		if (!mysqli_result(mysqli_query($link, "SELECT domain FROM alias WHERE address='$address' AND (domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
		{
			header("Location: do.php?event=".base64_encode("Permission denied"));
			die("Permission denied");
		}
		if (!ctype_alnum(str_replace(array('@', '.', '-'), '', $address)))
		{
			header("Location: do.php?event=".base64_encode("Mail address invalid"));
			die("Mail address invalid");
		}
		$mystring = "DELETE FROM alias WHERE address='$address' AND address NOT IN (SELECT username FROM mailbox)";
		if (!mysqli_query($link, $mystring))
		{
			header("Location: do.php?event=".base64_encode("MySQL query failed"));
			die("MySQL query failed");
		}
		header('Location: do.php?return=success');
	}
	public function delete_domain_admin()
	{
		if ($_SESSION['mailcow_cc_role'] != "admin")
		{
			header("Location: do.php?event=".base64_encode("Permission denied"));
			die("Permission denied");
		}
		$username = mysqli_real_escape_string($link, $_POST['username']);
		if (!ctype_alnum(str_replace(array('@', '.', '-'), '', $username)))
		{
			header("Location: do.php?event=".base64_encode("Invalid username"));
			die("Invalid username");
		}
		$delete_domain = "DELETE FROM domain_admins WHERE username='$username';";
		$delete_domain .= "DELETE FROM admin WHERE username='$username';";
		if (!mysqli_multi_query($link, $delete_domain))
		{
			header("Location: do.php?event=".base64_encode("MySQL query failed"));
			die("MySQL query failed");
		}
		while ($link->next_result())
		{
			if (!$link->more_results()) break;
		}
		header('Location: do.php?return=success');
	}
	public function delete_alias_domain()
	{
		$alias_domain = mysqli_real_escape_string($link, $_POST['alias_domain']);
		global $logged_in_role;
		global $logged_in_as;
		if (!mysqli_result(mysqli_query($link, "SELECT target_domain FROM alias_domain WHERE alias_domain='$alias_domain' AND (target_domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
		{
			header("Location: do.php?event=".base64_encode("Permission denied"));
			die("Permission denied");
		}
		if (!ctype_alnum(str_replace(array('.', '-'), '', $alias_domain)))
		{
			header("Location: do.php?event=".base64_encode("Domain name invalid"));
			die("Domain name invalid");
		}
		$mystring = "DELETE FROM alias_domain WHERE alias_domain='$alias_domain'";
		if (!mysqli_query($link, $mystring))
		{
			header("Location: do.php?event=".base64_encode("MySQL query failed"));
			die("MySQL query failed");
		}
		header('Location: do.php?return=success');
	}
	public function delete_mailbox()
	{
		$username = mysqli_real_escape_string($link, $_POST['username']);
		global $logged_in_role;
		global $logged_in_as;
		if (!mysqli_result(mysqli_query($link, "SELECT domain FROM mailbox WHERE username='$username' AND (domain NOT IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'!='$logged_in_role')")))
		{
			header("Location: do.php?event=".base64_encode("Permission denied"));
			die("Permission denied");
		}
		if (!filter_var($username, FILTER_VALIDATE_EMAIL))
		{
			header("Location: do.php?event=".base64_encode("Mail address invalid"));
			die("Mail address invalid");
		}
		$delete_user = "DELETE FROM alias WHERE goto='$username';";
		$delete_user .= "DELETE FROM quota2 WHERE username='$username';";
		$delete_user .= "DELETE FROM calendarobjects WHERE calendarid IN (SELECT id from calendars where principaluri='principals/$username');";
		$delete_user .= "DELETE FROM cards WHERE addressbookid IN (SELECT id from calendars where principaluri='principals/$username');";
		$delete_user .= "DELETE FROM mailbox WHERE username='$username';";
		$delete_user .= "DELETE FROM users WHERE username='$username';";
		$delete_user .= "DELETE FROM principals WHERE uri='principals/$username';";
		$delete_user .= "DELETE FROM principals WHERE uri='principals/$username/calendar-proxy-read';";
		$delete_user .= "DELETE FROM principals WHERE uri='principals/$username/calendar-proxy-write';";
		$delete_user .= "DELETE FROM addressbooks WHERE principaluri='principals/$username';";
		$delete_user .= "DELETE FROM calendars WHERE principaluri='principals/$username';";
		if (!mysqli_multi_query($link, $delete_user))
		{
			header("Location: do.php?event=".base64_encode("MySQL query failed"));
			die("MySQL query failed");
		}
		while ($link->next_result())
		{
			if (!$link->more_results()) break;
		}
		header('Location: do.php?return=success');
	}
}
?>