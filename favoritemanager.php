<?php
	require_once("./databasemanager.php");

	$response = '';
	$listing_id = $_POST['lid'];
	//$listing_id = 1602;

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