<?php //common_html

class CommonHTML
{
	public static function get_header()
	{
		echo "<!DOCTYPE html>
		<html>
		<head>
		  <title>CLBASE - Craigslist Boston Area Search Engine</title>
		  <style type='text/css'>
		    @import 'css/application.css';
		  </style>
		</head>
		<body>
		  <div id='banner'>
		    CLBASE
		  </div>
		  <div id='sub_banner'>
		  	<a href='listings.php'>All Listings</a> | 
		    <a href='favorites.php'>Favorites</a> | 
		    <a href='sharedlistings.php'>Shared Listings</a> | 
		    <a href='settings.php'>Settings</a> 
		  </div>";
	}

	public static function get_footer()
	{
		echo "</body>
		</html>";
	}
}