<?php //insert search location script

	require_once('./databasemanager.php');

	$dbmngr = new DatabaseManager();


	$location = $_POST['location'];

	$result = $dbmngr->InsertSearchLocation($location);

	$response = 'failed';

	if($result->GetSuccessFlag())
	{
		$response = $result->GetPayload();
	}

	echo json_encode($response);