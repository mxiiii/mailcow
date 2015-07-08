<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading">Login</div>
		<div class="panel-body">
			<form class="form-signin" method="post" action="do_login">
				<input name="login_user" type="name" id="login_user" class="form-control" placeholder="Username" required autofocus>
				<input name="pass_user" type="password" id="pass_user" class="form-control" placeholder="Password" required>
				<input type="submit" class="btn btn-sm btn-success" value="Login">
				<p><small><strong>Hint:</strong> Use "mailcow_resetadmin" to reset the password.</small></p>
			</form>
		</div>
	</div>
</div>