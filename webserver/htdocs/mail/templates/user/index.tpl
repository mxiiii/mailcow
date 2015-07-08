<div class="container">

	<div class="panel panel-default">
		<div class="panel-heading">Change password</div>
		<div class="panel-body">
			<form class="form-horizontal" role="form" method="post">
				<input type="hidden" name="mailboxaction" value="setuserpassword">
				<input type="hidden" name="user_now" value="<?php echo $logged_in_as; ?>">
				
				<div class="form-group">
					<label class="control-label col-sm-3" for="user_old_pass">Current password:</label>
					<div class="col-sm-5">
						<input type="password" class="form-control" name="user_old_pass" id="user_old_pass" required>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-3" for="user_new_pass">
						<small>New password:</small>
					</label>

					<div class="col-sm-5">
						<input type="password" class="form-control" name="user_new_pass" id="user_new_pass" required>
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-3" for="user_new_pass2">
						<small>New password (repeat):</small>
					</label>
			
					<div class="col-sm-5">
						<input type="password" class="form-control" name="user_new_pass2" id="user_new_pass2" required>
					</div>
				</div>

				<div class="form-group">        
					<div class="col-sm-offset-3 col-sm-9">
						<button type="submit" class="btn btn-default btn-raised btn-sm">Change password</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">Calendars and Contacts</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-striped" id="domainadminstable">
					<thead>
						<tr>
							<th>Components</th>
							<th>URI</th>
							<th>Display name</th>
							<th>Export</th>
							<th>Link</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>{$cal['components']}</td>
							<td>{$cal['uri']}</td>
							<td>{$cal['displayname']}</td>
							<td><a href="https://dav.{$hostname_1}.{$hostname_2}/calendars/{$smarty.session['username']}/{$cal['uri']}?export">Download (ICS format)</a></td>
							<td><a href="https://dav.{$hostname_1}.{$hostname_2}/calendars/{$smarty.session['username']}/{$cal['uri']}">Open</a></td>
						</tr>

						<tr>
							<td>Address book</td>
							<td>{$ad['uri']}</td>
							<td>{$ad['displayname']}</td>
							<td><a href="https://dav.{$hostname_1}.{$hostname_2}/addressbooks/{$smarty.session['username']}/{$cal['uri']}?export">Download (VCF format)</a></td>
							<td><a href="https://dav.{$hostname_1}.{$hostname_2}/addressbooks/{$smarty.session['username']}/{$cal['uri']}">Open</a></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>