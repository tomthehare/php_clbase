<?php
	require_once("./databasemanager.php");

	$response = '';

	if($_POST['lid'] != null)
	{
		$dbmngr = new DatabaseManager();

		$result = $dbmngr->ReverseFavorite($_POST['lid']);

	}





?>