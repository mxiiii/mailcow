<?php
class UserController extends BaseController
{
	public function index()
	{
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