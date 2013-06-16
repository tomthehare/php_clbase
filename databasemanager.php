<?php

require_once("./config.php");
require_once("./DatabaseResult.php");
require_once("./listing.php");

class DatabaseManager
{
	var $database;
	var $server;
	var $username;
	var $password;

	function DatabaseManager()
	{
		$this->server = "localhost";
		$this->database = "clbase_development";
		$this->username = "root";
		$this->password = "braindrain";
	}

	private function OpenConnection()
	{
		$con = mysqli_connect($this->server, $this->username, $this->password, $this->database);

		if(mysqli_connect_errno())
		{
			echo "FAILED TO CONNECT TO MYSQL " . mysqli_connect_error();
		}
		else
		{
			return $con;
		}
	}

	private function CloseConnection($con)
	{
		mysqli_close($con);
	}

	function ToString()
	{
		echo "server: $this->server<br/>";
		echo "database: $this->database<br/>";
		echo "username: $this->username<br/>";
		echo "password: $this->password<br/>";
	}

	function RetrieveListings($size = 200)
	{
		$result = new DatabaseResult(false, 'Have not checked db yet');
		$con = $this->OpenConnection();

		$query = "select * FROM clbase_development.listings order by `listing_date` desc limit ?";

		//Prepare a statement
		$stmt = mysqli_stmt_init($con);

		if(mysqli_stmt_prepare($stmt, $query))
		{
			$stmt->bind_param('i', $size);

			if($stmt->execute())
			{
				if(!$db_results = $stmt->get_result())
				{
					$result->SetSuccessFlag(false);
					$result->SetReason("Trouble retrieving results in GetListings()");
				}
				else
				{
					$rows = $db_results->fetch_all();
					$listings = array();

					foreach($rows as $row)
					{
						$listing = new Listing();

						$listing->SetID($row[0]);
						$listing->SetTitle($row[1]);
						$listing->SetDate($row[2]);
						$listing->SetPrice($row[3]);
						$listing->SetBedroomCount($row[4]);
						$listing->SetLocation($row[5]);
						$listing->SetImageFlag($row[6]);
						$listing->SetLink($row[9]);
						$listing->SetFavoriteFlag($row[11]);

						array_push($listings, $listing);
					}

					$result->SetSuccessFlag(true);
					$result->SetPayload($listings);
				}

				mysqli_stmt_close($stmt);
			}
			else
			{
				$result->SetSuccessFlag(false);
				$result->SetReason("Trouble executing query in GetListings()");
			}
		}
		else
		{
			$result->SetSuccessFlag(false);
			$result->SetReason("Trouble setting up statement in GetListings()");
		}

		$this->CloseConnection($con);

		return $result;
	}

	function RetrieveFavoriteListings()
	{
		$result = new DatabaseResult(false, 'Have not checked db yet');
		$con = $this->OpenConnection();

		$query = "select * FROM clbase_development.listings where `favorite` = 1 order by `listing_date` desc";

		//Prepare a statement
		$stmt = mysqli_stmt_init($con);

		if(mysqli_stmt_prepare($stmt, $query))
		{
			if($stmt->execute())
			{
				if(!$db_results = $stmt->get_result())
				{
					$result->SetSuccessFlag(false);
					$result->SetReason("Trouble retrieving results in GetFavoriteListings()");
				}
				else
				{
					$rows = $db_results->fetch_all();
					$listings = array();

					foreach($rows as $row)
					{
						$listing = new Listing();

						$listing->SetID($row[0]);
						$listing->SetTitle($row[1]);
						$listing->SetDate($row[2]);
						$listing->SetPrice($row[3]);
						$listing->SetBedroomCount($row[4]);
						$listing->SetLocation($row[5]);
						$listing->SetImageFlag($row[6]);
						$listing->SetLink($row[9]);
						$listing->SetFavoriteFlag($row[11]);

						array_push($listings, $listing);
					}

					$result->SetSuccessFlag(true);
					$result->SetPayload($listings);
				}

				mysqli_stmt_close($stmt);
			}
			else
			{
				$result->SetSuccessFlag(false);
				$result->SetReason("Trouble executing query in GetFavoriteListings()");
			}
		}
		else
		{
			$result->SetSuccessFlag(false);
			$result->SetReason("Trouble setting up statement in GetFavoriteListings()");
		}

		$this->CloseConnection($con);

		return $result;
	}

	//will return the updated favorite value
	function ReverseFavorite($id)
	{
		$result = new DatabaseResult(false, 'Did nothing');
		$new_state = null;

		//get current state first
		$get_state_result = $this->GetFavoriteState($id);

		if($get_state_result->GetSuccessFlag())
		{	
			$current_state = $get_state_result->GetPayload();

			$new_state = !$current_state;

			//now save new state
			$reverse_state_result = $this->SaveFavoriteState($id, $new_state);

			if(!$reverse_state_result->GetSuccessFlag())
			{
				$result->SetReason('Could not save new state for id='.$id);
				$result->SetSuccessFlag(false);		
			}
			else
			{
				$result->SetSuccessFlag(true);
				$result->SetReason('Successfully flipped state');
				$result->SetPayload($reverse_state_result->GetPayload());
			}		
		}
		else
		{
			$result->SetReason('Could not retrieve current state for id='.$id);
			$result->SetSuccessFlag(false);
		}

		// echo "<h1>final result from ReverseFavorite()</h1>";
		// echo var_dump($result);
		return $result;
	}

	private function SaveFavoriteState($id, $state)
	{
		$result = new DatabaseResult(false, 'Did nothing');

		$con = $this->OpenConnection();

		$query = "update clbase_development.listings set `favorite` = ? where `id` = ?;";

		$stmt = mysqli_stmt_init($con);

		if(mysqli_stmt_prepare($stmt, $query))
		{
			$stmt->bind_param('ii', $state, $id);

			if($stmt->execute())
			{
				$result->SetReason('state updated successfully');
				$result->SetSuccessFlag(true);
				$result->SetPayload($state);
			}
			else
			{
				$result->SetSuccessFlag(false);
				$result->SetReason("Trouble executing: $query");
			}
		}
		else
		{
			$result->SetSuccessFlag(false);
			$result->SetReason("Trouble setting up statement in SaveFavoriteState()");
		}

		$this->CloseConnection($con);

		// echo "<h1>result coming back from SaveState()</h1>";
		// echo var_dump($result);

		return $result;
	}

	private function GetFavoriteState($id)
	{
		$result = new DatabaseResult(false, 'Did nothing');

		$con = $this->OpenConnection();

		$query = "select `favorite` from listings where `id` = ?";

		$stmt = mysqli_stmt_init($con);

		if(mysqli_stmt_prepare($stmt, $query))
		{
			$stmt->bind_param('i', $id);

			if($stmt->execute())
			{
				if(!$db_results = $stmt->get_result())
				{
					$result->SetSuccessFlag(false);
					$result->SetReason("Trouble retrieving results in ReverseFavorite()");
				}
				else
				{
					$state_results = $db_results->fetch_all();

					if(count($state_results) > 0)
					{
						$favorite_state = $state_results[0][0];

						$result->SetReason('state retrieved successfully');
						$result->SetSuccessFlag(true);
						$result->SetPayload($favorite_state);
					}
					else
					{
						$result->SetSuccessFlag(false);
						$result->SetReason("No record found with id = $id");
						$result->SetPayload(null);
					}
				}
			}
			else
			{
				$result->SetSuccessFlag(false);
				$result->SetReason("Trouble executing: $query");
			}
		}
		else
		{
			$result->SetSuccessFlag(false);
			$result->SetReason("Trouble setting up statement in GetFavoriteState()");
		}

		$this->CloseConnection($con);

		// echo "<h1>result coming from GetState(id)</h1>";
		// echo var_dump($result);

		return $result;
	}

}