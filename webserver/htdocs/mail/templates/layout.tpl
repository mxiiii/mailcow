<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$hostname}</title>
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
<link href="{$css_asset_path}material.min.css" rel="stylesheet">
<link href="{$css_asset_path}ripples.min.css" rel="stylesheet">
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

{include file="navigation.tpl"}

{include file="flash_messages.tpl"}

{include file="{$content}"}
{include file="footer.tpl"}