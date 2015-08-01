<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Edit</h3>
				</div>
				<div class="panel-body">
<?php
if (!ctype_alnum(str_replace(array('.', '@', '-'), '', $_GET["deletealias"])) || empty($_GET["deletealias"])) {
	header("Location: do.php?event=".base64_encode("Your provided alias name is invalid"));
	die("Your provided alias name is invalid");
}
else
{
	$deletealias = mysqli_real_escape_string($link, $_GET["deletealias"]);
	if (mysqli_result(mysqli_query($link, "SELECT goto domain FROM alias WHERE (address='$deletealias' AND goto!='$deletealias') AND (domain IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'='$logged_in_role')")))
	{
		echo '<div class="alert alert-warning" role="alert"><strong>Warning:</strong> You are about to delete an alias!</div>';
		echo "<p>The following users will no longer receive mail for/send mail from alias address <strong>$deletealias:</strong></p>";
		$query = "SELECT goto, domain FROM alias WHERE (address='$deletealias' AND goto!='$deletealias) AND ((domain IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'='$logged_in_role'))";
		$result = mysqli_query($link, $query);
		echo "<ul>";
		while ($row = mysqli_fetch_array($result)) {
			echo "<li>", $row['goto'], "</li>";
		}
		echo "</ul>";
?>
				<form class="form-horizontal" role="form" method="post" action="/delete_alias">
				<input type="hidden" name="address" value="<?php echo $deletealias ?>">
				<input type="hidden" name="mailboxaction" value="deletealias">
					<div class="form-group">
						<div class="col-sm-offset-1 col-sm-10">
							<button type="submit" class="btn btn-default btn-sm">Delete</button>
						</div>
					</div>
<?php
	}
	else {
		echo 'Your provided alias name does not exist or cannot be removed.';
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