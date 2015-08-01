<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Edit</h3>
				</div>
				<div class="panel-body">
<?php
if (!ctype_alnum(str_replace(array('@', '.', '-'), '', $_GET["deletedomainadmin"])) || empty($_GET["deletedomainadmin"]))
{
	header("Location: do.php?event=".base64_encode("Domain administrator name invalid"));
	die("Domain administrator name invalid");
}
else
{
	$deletedomainadmin = mysqli_real_escape_string($link, $_GET["deletedomainadmin"]);
	if (mysqli_result(mysqli_query($link, "SELECT username FROM domain_admins WHERE username='$deletedomainadmin'")) && $logged_in_role == "admin")
	{
		echo '<div class="alert alert-warning" role="alert"><strong>Warning:</strong> You are about to delete a domain administrator!</div>';
		echo "<p>The domain administrator <strong>$deletedomainadmin</strong> will not be able to login after deletion.</p>";
		?>
					<form class="form-horizontal" role="form" method="post" action="/delete_domain_admin">
					<input type="hidden" name="username" value="<?php echo $deletedomainadmin ?>">
					<input type="hidden" name="mailboxaction" value="deletedomainadmin">
						<div class="form-group">
							<div class="col-sm-offset-1 col-sm-10">
								<button type="submit" class="btn btn-default btn-sm">Delete</button>
							</div>
						</div>
		<?php
	}
	else {
		echo 'Action not supported.';
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