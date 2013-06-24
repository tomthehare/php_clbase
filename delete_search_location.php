<?php //delete search location script

	require_once('./databasemanager.php');
	require_once("common.php");

	$id = $_POST['id'];

	$dbmngr = new DatabaseManager();

	$result = $dbmngr->DeleteSearchLocation($id);

	$response = -1;

	if($result->GetSuccessFlag())
	{
		$response = $result->GetPayload();
	}

	echo json_encode($response);