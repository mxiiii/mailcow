<?php
class UserController extends BaseController
{
	public function index()
	{

		// Kalender usw auslesen
		$row_cal = Core::$link->get('calendars', [
			'components',
			'uri',
			'displayname'
		],
		[
			'principaluri' => 'principals/'.$_SESSION['username']
		]);

		// einige Sachen ersetzen
		$row_cal['components'] = str_replace(['VEVENT', 'VTODO', ','], ['Calendar', 'Tasks', ', '], $row_cal['components']);

		// Adressbuch auslesen
		$row_ad = Core::$link->get('addressbooks', [
			'uri',
			'displayname'
		],
		[
			'principaluri' => 'principals/'.$_SESSION['username']
		]);

		// Variablen übergeben
		Core::$template->assign('cal', $row_cal);
		Core::$template->assign('ad', $row_ad);
	}

	
	public function login()
	{
	}

	
	public function doLogin($username = false, $password = false)
	{
		if($username) $_POST['login_user'] = $username;
		if($password) $_POST['pass_user'] = $password;

		if(!ctype_alnum(str_replace(array('@', '.', '-'), '', $_POST['login_user']))) return loc('login');

		$pass = escapeshellcmd($_POST['pass_user']);
		$row = Core::$link->get('admin', ['password', 'superadmin'], [
			'AND' => [
				'username' => $_POST['login_user'],
				'active' => '1',
			]
		]);

		if($username && $password) return $row;

		if(strpos(shell_exec('echo '.$pass.' | doveadm pw -s SHA512-CRYPT -t \''.$row['password'].'\''), 'verified') !== false)
		{
			if($row['superadmin'] == 1) $_SESSION['role'] = 'admin';
			if($row['superadmin'] == 0) $_SESSION['role'] = 'domainadmin';
			$_SESSION['username'] = escapeshellcmd($_POST['login_user']);
			$_SESSION['logged_in'] = true;
		}
		else
		{
			$pass = Core::$link->get('mailbox', 'password', [
				'AND' => [
					'username' => $_POST['login_user'],
					'active' => '1',
				]
			]);

			if($pass == false) loc('login');
			else
			{
				$_SESSION['username'] = escapeshellcmd($_POST['login_user']);
				$_SESSION['logged_in'] = true;
				$_SESSION['role'] = 'user';
			}
		}

		loc('admin');
	}

	
	public function logout()
	{
		session_destroy();
		unset($_SESSION);
		loc('/');
	}


	public function set_fetch_mail()
	{
		if($_SESSION['role'] !== 'user') loc('admin');
		
		if(empty($_POST['imap_host']) || empty($_POST['imap_username']) || empty($_POST['imap_password']) || empty($_POST['imap_enc'])) loc('user');

		$host = explode(':', escapeshellcmd($_POST['imap_host']));
		$imap_host = $host[0];
		$imap_port = $host[1];

		$imap_username = escapeshellcmd($_POST['imap_username']);
		$imap_password = escapeshellcmd($_POST['imap_password']);
		$imap_enc = escapeshellcmd($_POST['imap_enc']);
		$imap_exclude = explode(',', str_replace([', ', ' , ', ' ,'], ',', escapeshellcmd($_POST['imap_exclude'])));

		$allowed_imap_connection = ['/ssl', '/tls', 'none'];
		if(!in_array($imap_enc, $allowed_imap_connection)) die('Invalid encryption mechanism!');

		if(!is_numeric($imap_port) || empty($imap_port)) loc('user');
		if(!ctype_alnum(str_replace(array('@', '.', '-', '\\', '/'), '', $imap_username)) || empty ($imap_username)) loc('user');
		if(!ctype_alnum(str_replace(array(', ', ' , ', ' ,', ' '), '', escapeshellcmd($_POST['imap_exclude']))) && !empty($_POST['imap_exclude'])) loc('user');
		if(!$imap = imap_open("{".$imap_host.":".$imap_port."/imap/novalidate-cert".$imap_enc."}", $imap_username, $imap_password, OP_HALFOPEN, 1)) loc('user');
		
		switch($imap_enc)
		{
			case '/ssl':
				$imap_enc = 'imaps';
				break;

			case '/tls':
				$imap_enc = 'starttls';
				break;

			case '':
				$imap_enc = '';
				break;

			default:
				$imap_enc = '';
				break;
		}

		if(count($imap_exclude) > 1)
		{
			foreach($imap_exclude as $each_exclude)
			{
				$exclude_parameter .= '-x '.$each_exclude.'* ';
			}
		}

		ini_set('max_execution_time', 3600);
		exec('sudo /usr/bin/doveadm -o imapc_port='.$imap_port.' -o imapc_ssl='.$imap_enc.' \
		-o imapc_host='.$imap_host.' \
		-o imapc_user='.$imap_username.' \
		-o imapc_password='.$imap_password.' \
		-o imapc_ssl_verify=no \
		-o ssl_client_ca_dir=/etc/ssl/certs \
		-o imapc_features="rfc822.size fetch-headers" \
		-o mail_prefetch_count=20 sync -1 \
		-x "Shared*" -x "Public*" -x "Archives*" '.$exclude_parameter.' \
		-R -U -u '.$logged_in_as.' imapc:', $out, $return);
		
		if ($return == '2') exec('sudo /usr/bin/doveadm quota recalc -A', $out, $return);

		loc('user');
	}


	public function change_password()
	{
		if($_SESSION['role'] !== 'user') loc('admin');
		if(empty($_POST['user_old_pass']) || empty($_POST['user_new_pass']) || empty($_POST['user_new_pass'])) loc('user');

		if(!ctype_alnum(str_replace(array('@', '.', '-'), '', $_SESSION['username'])) || empty($_SESSION['username'])) loc('user');

		$password_old = $_POST['user_old_pass'];
		$password_new = $_POST['user_new_pass'];
		$password_new2 = $_POST['user_new_pass'];

		if($password_new2 !== $password_new) loc('user');

		$row = $this->doLogin($_SESSION['username'], $password_old);

		print_r($row);
		die();
	}
}
?>