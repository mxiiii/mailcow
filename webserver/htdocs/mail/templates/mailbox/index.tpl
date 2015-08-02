<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
				<h3 class="panel-title">Domains</h3>
				<div class="pull-right">
					<span class="clickable filter" data-toggle="tooltip" title="Toggle table filter" data-container="body">
						<i class="glyphicon glyphicon-filter"></i>
					</span>
					<a href="do.php?adddomain"><span class="glyphicon glyphicon-plus"></span></a>
				</div>
				</div>
				<div class="panel-body">
					<input type="text" class="form-control" id="domaintable-filter" data-action="filter" data-filters="#domaintable" placeholder="Filter" />
				</div>
				<div class="table-responsive">
					<table class="table table-striped" id="domaintable">
						<thead>
							<tr>
								<th>Domain</th>
								<th>Aliases</th>
								<th>Mailboxes</th>
								<th>Max. quota per mailbox</th>
								<th>Domain Quota</th>
								<th>Active</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							{foreach $domains as $domain}
								<tr>
									<td>{$domain['domain']}</td>
									<td>{$domain['aliases']}</td>
									<td>{$domain['mailboxes']}</td>
									<td>{$domain['maxquota']} M</td>
									<td>{$domain['quota']} M</td>
									<td>{$domain['active']}</td>
									<td><a href="/deteledomainadmin/{$domain['username']}">delete</a> | <a href="/editdomainadmin/{$domain['username']}">edit</a></td>
								</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
