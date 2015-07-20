<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Edit</h3>
				</div>
				<div class="panel-body">
<?php
if (!filter_var($_GET["deletemailbox"], FILTER_VALIDATE_EMAIL))
{
	header("Location: do.php?event=".base64_encode("Your provided mailbox name is invalid"));
	die("Your provided alias name is invalid"); 
}
else
{
	$deletemailbox = mysqli_real_escape_string($link, $_GET["deletemailbox"]);
	if (mysqli_result(mysqli_query($link, "SELECT address, domain FROM alias WHERE address='$deletemailbox' AND (domain IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'='$logged_in_role')")))
	{
		echo '<div class="alert alert-warning" role="alert"><strong>Warning:</strong> You are about to delete a mailbox!</div>';
		echo "<p>The mailbox user <strong>$deletemailbox</strong> + its address books and calendars will be deleted.</p>";
		echo "<p>The user will also be removed from the alias addresses listed below (if any).</p>";
		echo "<ul>";
		$result = mysqli_query($link, "SELECT address FROM alias WHERE goto='$deletemailbox' and address!='$deletemailbox'");
		while ($row = mysqli_fetch_array($result)) {
			echo "<li>", $row['address'], "</li>";
		}
		echo "</ul>";
		?>
					<form class="form-horizontal" role="form" method="post" action="/delete_mailbox">
					<input type="hidden" name="mailboxaction" value="deletemailbox">
					<input type="hidden" name="username" value="<?php echo $deletemailbox ?>">
						<div class="form-group">
							<div class="col-sm-offset-1 col-sm-10">
								<button type="submit" class="btn btn-default btn-sm">Delete</button>
							</div>
						</div>
<?php
	}
	else {
		echo 'Your provided mailbox name does not exist.';
	}
}
?>
					</form>
				</div>
			</div>
		</div>
	</div>
<a href="#" onclick="window.history.back();return false;">&#8592; go back</a>
</div> <!-- /container -->
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="js/ripples.min.js"></script>
<script src="js/material.min.js"></script>
<script>
$(document).ready(function() {
	$.material.init();
});
</script>
</body>
</html>