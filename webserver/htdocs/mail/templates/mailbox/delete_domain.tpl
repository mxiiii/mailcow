<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Edit</h3>
				</div>
				<div class="panel-body">
<?php
if (!ctype_alnum(str_replace(".", '', $_GET["deletedomain"])) || empty($_GET["deletedomain"]))
{
	echo 'Your provided domain name is invalid.';
}
else {
	$deletedomain = mysqli_real_escape_string($link, $_GET["deletedomain"]);
	if (mysqli_result(mysqli_query($link, "SELECT domain FROM domain WHERE domain='$deletedomain' AND ((domain IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'='$logged_in_role'))")))
	{
		echo '<div class="alert alert-warning" role="alert"><strong>Warning:</strong> You are about to delete a domain!</div>';
		echo "<p>This will also delete domain alises assigned to the domain</p>";
		echo "<p><strong>Domain must be empty to be deleted!</b></p>";
?>
				<form class="form-horizontal" role="form" method="post" action="/delete_domain">
				<input type="hidden" name="mailboxaction" value="deletedomain">
				<input type="hidden" name="domain" value="<?php echo $deletedomain ?>">
					<div class="form-group">
						<div class="col-sm-offset-1 col-sm-10">
							<button type="submit" class="btn btn-default btn-sm">Delete</button>
						</div>
					</div>
<?php
	}
	else {
		echo 'Your provided domain name does not exist or cannot be removed.';
	}
}
?>
				</form>
				</div>
			</div>
		</div>
	</div>
<a href="#" onclick="window.history.back();return false;">&#8592; go back</a>
</div>