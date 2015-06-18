<div class="jumbotron">
	<div class="container">
		<h2>Welcome @ {$hostname}</h2>
		<p style="font-weight:300;font-size:24px;margin-right:151px;line-height:30px;margin-top:-2px"><i>Get cownnected...</i></h4>
		<div class="row">
			<div class="col-md-6">
				<small><b>IMAP (STARTTLS) or IMAPS</b></small>
				<ul class="ul-horizontal">
					<li><code>{$hostname}:143/tcp</code></li>
					<li><code>{$hostname}:993/tcp</code></li>
				</ul>
				<small><b>SMTP (STARTTLS)</b></small>
				<ul>
					<li><code>{$hostname}:587/tcp</code></li>
				</ul>
				<small><b>Cal- and CardDAV</b></small>
				<ul>
					<li><code>https://dav.{$hostname}/</code></li>
					<small>(Append <code>principals/you@{$hostname}{$hostname}/</code> on errors)</small>
				</ul>
				<small>Please use your full email address as login name.</small>
			</div>
		</div>
	</div>
</div>
<div class="container">
	<h4>Health check (© MXToolBox)</h4>
	<p>"The Domain Health Check will execute hundreds of domain/email/network performance tests to make sure all of your systems are online and performing optimally. The report will then return results for your domain and highlight critical problem areas for your domain that need to be resolved."</p>
	<a class="btn btn-material-grey" href="http://mxtoolbox.com/SuperTool.aspx?action=smtp:{$hostname}" target="_blank">Run &raquo;</a>
	<br />
</div>