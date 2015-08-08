<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
				<h3 class="panel-title">Edit</h3>
				</div>
				<div class="panel-body">
				<h4>Add domain alias</h4>
				<form class="form-horizontal" role="form" method="post" action="save_add_domain_alias">
					<input type="hidden" name="mailboxaction" value="addaliasdomain">
					<div class="form-group">
						<label class="control-label col-sm-2" for="alias_domain">Alias domain:</label>
						<div class="col-sm-10">
						<input type="text" class="form-control" name="alias_domain" id="alias_domain">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="name">Target domain:</label>
						<div class="col-sm-10">
							<select name="target_domain" size="1">
								{if $target_domains}
									{foreach $target_domains as $target_domain}
										<option value="{$target_domain['domain']}">{$target_domain['domain']}</option>
									{/foreach}
								{/if}
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
</div>