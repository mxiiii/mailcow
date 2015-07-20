<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Edit</h3>
				</div>
				<div class="panel-body">
<?php
if (!ctype_alnum(str_replace(array('@', '.'), '', $_GET["editdomainadmin"])) || empty($_GET["editdomainadmin"])) { 
	echo 'Your provided domain administrator username is invalid.';
}
else {
	$editdomainadmin = mysqli_real_escape_string($link, $_GET["editdomainadmin"]);
	if (mysqli_fetch_array(mysqli_query($link, "SELECT username FROM domain_admins")) && $logged_in_role == "admin") {
	$result = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM domain_admins WHERE username='$editdomainadmin'"));
?>
					<h4>Change assigned domains for domain admin <strong><?php echo $editdomainadmin ?></strong></h4>
					<br />
					<form class="form-horizontal" role="form" method="post" action="/edit_domain_admin">
					<input type="hidden" name="mailboxaction" value="editdomainadmin">
					<input type="hidden" name="username" value="<?php echo $editdomainadmin ?>">
						<div class="form-group">
							<label class="control-label col-sm-2" for="name">Target domain <small>(hold CTRL to select multiple domains)</small>:</label>
							<div class="col-sm-10">
								<select name="domain[]" size="5" multiple>
<?php
		$resultselect = mysqli_query($link, "SELECT domain FROM domain");
		while ($row = mysqli_fetch_array($resultselect))
		{
			echo "<option>", $row['domain'], "</option>";
		}
?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<div class="checkbox">
								<label><input type="checkbox" name="active" <?php if (isset($result['active']) && $result['active']=="1") { echo "checked"; }; ?>> Active</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" class="btn btn-success btn-sm">Submit</button>
							</div>
						</div>
					</form>
			<?php
	}
	else {
		echo 'Action not supported.';
	}
}
			?>
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