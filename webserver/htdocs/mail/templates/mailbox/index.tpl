<div class="container">
	<!-- Domains -->
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
						{if $domains}
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
						{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Domain Aliasases -->
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Domain Aliases</h3>
					<div class="pull-right">
						<span class="clickable filter" data-toggle="tooltip" title="Toggle table filter" data-container="body">
							<i class="glyphicon glyphicon-filter"></i>
						</span>
						<a href="do.php?addaliasdomain"><span class="glyphicon glyphicon-plus"></span></a>
					</div>
				</div>
				<div class="panel-body">
					<input type="text" class="form-control" id="domainaliastable-filter" data-action="filter" data-filters="#domainaliastable" placeholder="Filter" />
				</div>
				<div class="table-responsive">
					<table class="table table-striped" id="domainaliastable">
						<thead>
							<tr>
								<th>Alias domain</th>
								<th>Target domain</th>
								<th>Active</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						{if $domain_aliases}
							{foreach $domain_aliases as $domain_alias}
								<tr>
									<td>{$domain_alias['alias_domain']}</td>
									<td>{$domain_alias['target_domain']}</td>
									<td>{$domain_alias['active']}</td>
									<td><a href="/deletealiasdomain/{$domain_alias['username']}">delete</a></td>
								</tr>
							{/foreach}
						{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Mailboxes -->
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Mailboxes</h3>
					<div class="pull-right">
						<span class="clickable filter" data-toggle="tooltip" title="Toggle table filter" data-container="body">
							<i class="glyphicon glyphicon-filter"></i>
						</span>
						<a href="do.php?addmailbox"><span class="glyphicon glyphicon-plus"></span></a>
					</div>
				</div>
				<div class="panel-body">
					<input type="text" class="form-control" id="mailboxtable-filter" data-action="filter" data-filters="#mailboxtable" placeholder="Filter" />
				</div>
				<div class="table-responsive">
					<table class="table table-striped" id="mailboxtable">
						<thead>
							<tr>
								<th>Username</th>
								<th>Name</th>
								<th>Domain</th>
								<th>Quota</th>
								<th>In use</th>
								<th>Msg #</th>
								<th>Active</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						{if $mailboxes}
							{foreach $mailboxes as $mailbox}
								<tr>
									<td>{$mailbox['username']}</td>
									<td>{$mailbox['name']}</td>
									<td>{$mailbox['target_domain']}</td>
									<td>{$mailbox['domain']}</td>
									<td>{$mailbox['quota']}</td>
									<td>{$mailbox['messages']}</td>
									<td>{$mailbox['active']}</td>
									<td><a href="/deletemailbox/{$mailbox['username']}">delete</a> | <a href="/editdomainadmin/{$mailbox['username']}">edit</a></td>
								</tr>
							{/foreach}
						{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Aliases -->
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Aliases</h3>
					<div class="pull-right">
						<span class="clickable filter" data-toggle="tooltip" title="Toggle table filter" data-container="body">
							<i class="glyphicon glyphicon-filter"></i>
						</span>
						<a href="do.php?addalias"><span class="glyphicon glyphicon-plus"></span></a>
					</div>
				</div>
				<div class="panel-body">
					<input type="text" class="form-control" id="aliastable-filter" data-action="filter" data-filters="#aliastable" placeholder="Filter" />
				</div>
				<div class="table-responsive">
					<table class="table table-striped" id="aliastable">
						<thead>
							<tr>
								<th>Alias address</th>
								<th>Destination</th>
								<th>Domain</th>
								<th>Active</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						{if $aliases}
							{foreach $aliases as $alias}
								<tr>
									<td>{$alias['address']}</td>
									<td>{$alias['goto']}</td>
									<td>{$alias['domain']}</td>
									<td>{$alias['active']}</td>
									<td><a href="/deletealias/{$alias['address']}">delete</a></td>
								</tr>
							{/foreach}
						{/if}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
