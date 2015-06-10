<?php
require('library/common.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo HOSTNAME; ?></title>
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<link href="<?php echo CSS_ASSET_PATH; ?>material.min.css" rel="stylesheet">
<link href="<?php echo CSS_ASSET_PATH; ?>ripples.min.css" rel="stylesheet">
<?php
if (basename($_SERVER['PHP_SELF']) == "mailbox.php") {
?>
<style>
.row { margin-top: 40px;	padding: 0 10px; }
.clickable { cursor: pointer; }
.panel-heading div { margin-top: -18px; font-size: 15px; }
.panel-heading div span{ margin-left:5px; }
.panel-body{ display: none; }
</style>
<?php
}
?>
<style>
.navbar.navbar, .navbar-default.navbar {
  background-color: #914063;
}
a, a:hover, a:focus {
  color: #333;
}
.dropdown-menu>li>a:hover {
  color: #777 !important;
}
@media(max-width:767px)  {
	.dropdown-menu>li>a:hover {
		color: #f5f5f5 !important;
	}
}
</style>
</head>
<body>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/"><img src="<?php echo IMG_ASSET_PATH; ?>xs_mailcow.png" /></a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav navbar-right">
				<li><a href="/rc">Webmail</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Control center<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<?php if (isset($_SESSION['mailcow_cc_loggedin']) && $_SESSION['mailcow_cc_loggedin'] == "yes") {
							if ($logged_in_role == "admin") { ?>
						<li><a href="/admin.php">Administration</a></li>
							<?php } if ($logged_in_role == "admin" || $logged_in_role == "domainadmin") { ?>
						<li><a href="/mailbox.php">Mailboxes</a></li>
							<?php } if ($logged_in_role == "user") { ?>
						<li><a href="/mailbox.php">User settings</a></li>
						<?php } } else { ?>
						<li><a href="/admin.php">Login</a></li>
						<?php } ?>
					</ul>
				</li>
				<li class="divider"></li>
				<li>
					<a href="#" onclick="logout.submit()"><?php	if (isset($_SESSION['mailcow_cc_loggedin']) && $_SESSION['mailcow_cc_loggedin'] == "yes") { echo "Hello, <strong>$logged_in_as</strong> (logout)"; } ?></a>
				</li>
			</ul>
		</div><!--/.nav-collapse -->
	</div><!--/.container-fluid -->
</nav>
<form action="/admin.php" method="post" id="logout"><input type="hidden" name="logout"></form>
<div class="jumbotron">
	<div class="container">
		<h2>Welcome @ <?php echo HOSTNAME; ?></h2>
		<p style="font-weight:300;font-size:24px;margin-right:151px;line-height:30px;margin-top:-2px"><i>Get cownnected...</i></h4>
		<div class="row">
			<div class="col-md-6">
				<small><b>IMAP (STARTTLS) or IMAPS</b></small>
				<ul class="ul-horizontal">
					<li><code><?php echo HOSTNAME; ?>:143/tcp</code></li>
					<li><code><?php echo HOSTNAME; ?>:993/tcp</code></li>
				</ul>
				<small><b>SMTP (STARTTLS)</b></small>
				<ul>
					<li><code><?php echo HOSTNAME; ?>:587/tcp</code></li>
				</ul>
				<small><b>Cal- and CardDAV</b></small>
				<ul>
					<li><code>https://dav.<?php echo HOSTNAME; ?>/</code></li>
					<small>(Append <code>principals/you@<?php echo HOSTNAME.'.'.HOSTNAME; ?>/</code> on errors)</small>
				</ul>
				<small>Please use your full email address as login name.</small>
			</div>
		</div>
	</div>
</div>
<div class="container">
		<h4>Health check (© MXToolBox)</h4>
		<p>"The Domain Health Check will execute hundreds of domain/email/network performance tests to make sure all of your systems are online and performing optimally. The report will then return results for your domain and highlight critical problem areas for your domain that need to be resolved."</p>
		<a class="btn btn-material-grey" href="http://mxtoolbox.com/SuperTool.aspx?action=smtp:<?php echo HOSTNAME; ?>" target="_blank">Run &raquo;</a>
		<br />
</div> <!-- /container -->
<?php include(LIB_PATH.'includes/footer.php'); ?>