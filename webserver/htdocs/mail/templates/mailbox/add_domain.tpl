<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Edit</h3>
				</div>
				<div class="panel-body">
				<h4>Add domain</h4>
					<form class="form-horizontal" role="form" method="post" action="/save_add_domain">
						<input type="hidden" name="mailboxaction" value="adddomain">
						<div class="form-group">
							<label class="control-label col-sm-2" for="domain">Domain name:</label>
							<div class="col-sm-10">
							<input type="text" class="form-control" name="domain" id="domain" placeholder="Domain to receive mail for">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="description">Description:</label>
							<div class="col-sm-10">
							<input type="text" class="form-control" name="description" id="description" placeholder="Description">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="aliases">Max. aliases:</label>
							<div class="col-sm-10">
							<input type="number" class="form-control" name="aliases" id="aliases" value="200">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="mailboxes">Max. mailboxes:</label>
							<div class="col-sm-10">
							<input type="number" class="form-control" name="mailboxes" id="mailboxes" value="50">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="maxquota">Max. size per mailbox (MB):</label>
							<div class="col-sm-10">
							<input type="number" class="form-control" name="maxquota" id="maxquota" value="4096">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2" for="quota">Domain quota:</label>
							<div class="col-sm-10">
							<input type="number" class="form-control" name="quota" id="quota" value="10240">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<div class="checkbox">
								<label><input type="checkbox" name="backupmx"> Backup MX</label>
								</div>
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