<?php

	require_once("./databasemanager.php");

	$response = '';
	$listing_id_chunks = explode('_', $_POST['lid']);
	$listing_id = $listing_id_chunks[count($listing_id_chunks)-1];

	if($listing_id != null)
	{
		$dbmngr = new DatabaseManager();

		$result = $dbmngr->DeleteListing($listing_id);

		if($result->GetSuccessFlag())
		{
			$response = "true";
		}
		else
		{
			$response = $result->GetReason();
		}
	}

	//echo "<h1>json response</h1>";
	echo json_encode($response);
?>
