<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Edit</h3>
				</div>
				<div class="panel-body">
<?php
if (!ctype_alnum(str_replace(array('.', '-'), '', $_GET["deletealiasdomain"])) || empty($_GET["deletealiasdomain"]))
{
	header("Location: do.php?event=".base64_encode("Alias domain name invalid"));
	die("Alias domain name invalid");
}
else {
	$deletealiasdomain = mysqli_real_escape_string($link, $_GET["deletealiasdomain"]);
	if (mysqli_result(mysqli_query($link, "SELECT alias_domain, target_domain FROM alias_domain WHERE alias_domain='$deletealiasdomain' AND (target_domain IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'='$logged_in_role')")))
	{
		echo '<div class="alert alert-warning" role="alert"><strong>Warning:</strong> You are about to delete an alias domain!</div>';
		echo "<p>The server will stop accepting mails for the domain name <strong>$deletealiasdomain</strong>.</p>";
?>
				<form class="form-horizontal" role="form" method="post" action="/delete_alias_domain">
				<input type="hidden" name="alias_domain" value="<?php echo $deletealiasdomain ?>">
				<input type="hidden" name="mailboxaction" value="deletealiasdomain">
					<div class="form-group">
						<div class="col-sm-offset-1 col-sm-10">
							<button type="submit" class="btn btn-default btn-sm">Delete</button>
						</div>
					</div>
<?php
	}
	else {
		echo 'Your provided alias domain name does not exist or cannot be removed.';
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