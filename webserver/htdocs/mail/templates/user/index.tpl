<div class="container">

	<!-- Change Password -->
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

	<!-- Calendars and Contacts -->
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

	<!-- Fetch E-Mails -->
	<div class="panel panel-default">
		<div class="panel-heading">Fetch mails</div>
		<div class="panel-body">
			<p>This is <b>not a recurring task</b>. This feature will perform a one-way synchronisation and leave the remote server as it is, no mails will be deleted on either sides.</p>
			<p>The first synchronisation may take a while.</p>
			<small>
				<form class="form-horizontal" role="form" method="post" action="/set_fetch_mail">
					<input type="hidden" name="mailboxaction" value="addfetchmail">
					
					<div class="form-group">
						<label class="control-label col-sm-2" for="imap_host">IMAP Host (with Port)</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="imap_host" id="imap_host" placeholder="remote.example.com:993">
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-2" for="imap_username">IMAP username:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="imap_username" id="imap_username">
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-2" for="imap_password">IMAP password:</label>
						<div class="col-sm-10">          
							<input type="password" class="form-control" name="imap_password" id="imap_password">
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-2" for="imap_exclude">Exclude folders:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="imap_exclude" id="imap_exclude" placeholder="Folder1, Folder2, Folder3">
						</div>
					</div>

					<div class="form-group">   
						<div class="col-sm-offset-2 col-sm-10">
							<div class="radio">
								<label><input type="radio" name="imap_enc" value="/ssl" checked>SSL</label>
							</div>
							<div class="radio">
								<label><input type="radio" name="imap_enc" value="/tls" >STARTTLS</label>
							</div>
							<div class="radio">
								<label><input type="radio" name="imap_enc" value="none">None (this will try STARTTLS)</label>
							</div>
						</div>
					</div>
					<div class="form-group">        
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" class="btn btn-success btn-sm">Sync now</button>
						</div>
					</div>
				</form>
			</small>
		</div>
	</div>
</div>