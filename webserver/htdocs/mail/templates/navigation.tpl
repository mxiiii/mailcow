<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/"><img src="{$img_asset_path}xs_mailcow.png" /></a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="/rc">Webmail</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Control center<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						{if (isset($_SESSION['mailcow_cc_loggedin']) && $_SESSION['mailcow_cc_loggedin'] == "yes")}
							{if $logged_in_role == "admin" }
								<li>
									<a href="/admin">Administration</a>
								</li>
							{/if}
							
							{if $logged_in_role == "admin" || $logged_in_role == "domainadmin"}
								<li>
									<a href="/mailbox.php">Mailboxes</a>
								</li>
							{/if}

							{if $logged_in_role == "user"}
								<li>
									<a href="/mailbox">User settings</a>
								</li>
							{/if}
						{else}
							<li>
								<a href="/admin">Login</a>
							</li>
						{/if}
					</ul>
				</li>
				{if isset($_SESSION['mailcow_cc_loggedin']) && $_SESSION['mailcow_cc_loggedin'] == "yes"}
					<li class="divider"></li>
					<li>
						<a href="#" onclick="logout.submit()">
							Hello, <strong>{$logged_in_as}</strong> (logout)
						</a>
					</li>
				{/if}
			</ul>
		</div>
	</div>
</nav>