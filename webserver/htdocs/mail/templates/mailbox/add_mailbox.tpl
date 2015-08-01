<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Edit</h3>
				</div>
				<div class="panel-body">
				<h4>Add a mailbox</h4>
				<form class="form-horizontal" role="form" method="post" action="/add_mailbox">
				<input type="hidden" name="mailboxaction" value="addmailbox">
					<div class="form-group">
						<label class="control-label col-sm-2" for="local_part">Mailbox Alias (left part of mail address) <small>(alphanumeric)</small>:</label>
						<div class="col-sm-10">
							<input type="text" pattern="[a-zA-Z0-9.- ]+" class="form-control" name="local_part" id="local_part" required>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="name">Select domain:</label>
						<div class="col-sm-10">
							<select name="domain" size="1">
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
						<label class="control-label col-sm-2" for="name">Name:</label>
						<div class="col-sm-10">
						<input type="text" class="form-control" name="name" id="name">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="quota">Quota (MB), 0 = unlimited:</label>
						<div class="col-sm-10">
						<input type="number" class="form-control" name="quota" id="quota" value="1024">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="password">Password:</label>
						<div class="col-sm-10">
						<input type="password" class="form-control" name="password" id="password" placeholder="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="password2">Password (repeat):</label>
						<div class="col-sm-10">
						<input type="password" class="form-control" name="password2" id="password2" placeholder="">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="default_cal">Default calendar name:</label>
						<div class="col-sm-10">
						<input type="text" class="form-control" name="default_cal" id="default_cal" value="Calendar">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="default_card">Default address book name:</label>
						<div class="col-sm-10">
						<input type="text" class="form-control" name="default_card" id="default_card" value="Address book">
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
</div>