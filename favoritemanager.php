<?php
	require_once("./databasemanager.php");
	require_once("common.php");

	$response = '';
	$listing_id_chunks = explode('_', $_POST['lid']);
	$listing_id = $listing_id_chunks[count($listing_id_chunks)-1];

	if($listing_id != null)
	{
		$dbmngr = new DatabaseManager();

		$result = $dbmngr->ReverseFavorite($listing_id);

		if($result->GetSuccessFlag())
		{
			// echo "<h1>new favorite state: </h1>";
			// echo var_dump($result->GetPayload());

			$response = $result->GetPayload() == true ? "Unfavorite" : "Favorite";
		}
	}

	//echo "<h1>json response</h1>";
	echo json_encode($response);
?>