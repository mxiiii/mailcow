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

	public function doLogin()
	{
		if (!ctype_alnum(str_replace(array('@', '.', '-'), '', $_POST['login_user']))) return header('Location: /login');

		$pass = escapeshellcmd($_POST['pass_user']);
		$row = Core::$link->get('admin', ['password', 'superadmin'], [
			'AND' => [
				'username' => $_POST['login_user'],
				'active' => '1',
			]
		]);

		if(strpos(shell_exec('echo '.$pass.' | doveadm pw -s SHA512-CRYPT -t \''.$row['password'].'\''), 'verified') !== false)
		{
			if($row['superadmin'] == 1) $_SESSION['role'] = 'admin';
			if($row['superadmin'] == 0) $_SESSION['role'] = 'domainadmin';
		}
		else $_SESSION['role'] = 'user';
		$_SESSION['username'] = $_POST['login_user'];
		$_SESSION['logged_in'] = true;

		header('Location: /admin');
	}

	public function logout()
	{
		session_destroy();
		unset($_SESSION);
		header('Location: /');
	}
}
?>