<div class="container">
	<div class="row">
		<div class="col-md-14">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Edit</h3>
				</div>
				<div class="panel-body">
				<h4>Add alias</h4>
				<form class="form-horizontal" role="form" method="post" action="/add_alias">
					<input type="hidden" name="mailboxaction" value="addalias">
					<div class="form-group">
						<label class="control-label col-sm-2" for="address">Alias address <small>(full e-mail address OR @domain.tld for <span style='color:#ec466a'>catch-all</span>)</small>:</label>
						<div class="col-sm-10">
						<input type="text" class="form-control" name="address" id="address">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2" for="goto">Destination <small>(full e-mail address)</small>:</label>
						<div class="col-sm-10">
						<input type="email" class="form-control" name="goto" id="goto">
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