<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
				<h3 class="panel-title">Edit</h3>
				</div>
				<div class="panel-body">
				<h4>Add domain alias</h4>
				<form class="form-horizontal" role="form" method="post" action="/add_domain_alias">
					<input type="hidden" name="mailboxaction" value="addaliasdomain">
					<div class="form-group">
						<label class="control-label col-sm-2" for="alias_domain">Alias domain:</label>
						<div class="col-sm-10">
						<input type="text" pattern="\b((?=[a-z0-9-]{1,63}\.)[a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,63}\b" class="form-control" name="alias_domain" id="alias_domain">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="name">Target domain:</label>
						<div class="col-sm-10">
							<select name="target_domain" size="1">
<?php
$result = mysqli_query($link, "SELECT domain FROM domain WHERE domain IN (SELECT domain from domain_admins WHERE username='$logged_in_as') OR 'admin'='$logged_in_role'");
while ($row = mysqli_fetch_array($result))
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
							<label><input type="checkbox" name="active" checked> Active</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" class="btn btn-success btn-sm">Submit</button>
						</div>
					</div>
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