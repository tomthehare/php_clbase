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

	function DeleteSearchLocation($id)
	{
		$result = new DatabaseResult(false, 'Have not checked db yet');
		$con = $this->OpenConnection();

		$query = "delete from search_locations where `idsearch_locations` = ?";

		//Prepare a statement
		$stmt = mysqli_stmt_init($con);

		if(mysqli_stmt_prepare($stmt, $query))
		{
			$stmt->bind_param('i', $id);

			if($stmt->execute())
			{
				$result->SetSuccessFlag(true);
				$result->SetPayload($id);
				mysqli_stmt_close($stmt);
			}
			else
			{
				$result->SetSuccessFlag(false);
				$result->SetReason("Trouble executing query in DeleteSearchTerm()");
			}
		}
		else
		{
			$result->SetSuccessFlag(false);
			$result->SetReason("Trouble setting up statement in DeleteSearchTerm()");
		}

		$this->CloseConnection($con);

		return $result;
	}

	function GetSearchLocations($specific_location = null)
	{
		$result = new DatabaseResult(false, 'Have not checked db yet');
		$con = $this->OpenConnection();

		$query = "select * from search_locations";

		if($specific_location != null)
		{
			$query = $query . " where `location` = ?";
		}

		//Prepare a statement
		$stmt = mysqli_stmt_init($con);

		if(mysqli_stmt_prepare($stmt, $query))
		{
			if($specific_location != null)
			{
				$stmt->bind_param('s', $specific_location);
			}

			if($stmt->execute())
			{
				if(!$db_results = $stmt->get_result())
				{
					$result->SetSuccessFlag(false);
					$result->SetReason("Trouble retrieving results in GetSearchLocations()");
				}
				else
				{
					$rows = $db_results->fetch_all();
					$locations = array();

					foreach($rows as $row)
					{
						$locations[$row[0]] = $row[1];
					}

					$result->SetSuccessFlag(true);
					$result->SetPayload($locations);
				}

				mysqli_stmt_close($stmt);
			}
			else
			{
				$result->SetSuccessFlag(false);
				$result->SetReason("Trouble executing query in GetSearchLocations()");
			}
		}
		else
		{
			$result->SetSuccessFlag(false);
			$result->SetReason("Trouble setting up statement in GetSearchLocations()");
		}

		$this->CloseConnection($con);

		return $result;
	}

	function InsertSearchLocation($location)
	{
		$result_master = new DatabaseResult(false, 'nothing found yet');

		$result = $this->CheckDatabaseForSearchLocation($location);

		if($result->GetSuccessFlag())
		{
			if($result->GetPayload() < 0)
			{
				//insert new location
				$insert_result = $this->insert_search_location($location);

				if($insert_result->GetSuccessFlag())
				{
					$get_result = $this->CheckDatabaseForSearchLocation($location);
					if($get_result->GetSuccessFlag())
					{
						$id = $get_result->GetPayload();

						$result_master->SetSuccessFlag(true);
						$result_master->SetPayload($id);					}
					else
					{
						$result_master->SetSuccessFlag(false);
						$result_master->SetReason($get_result->GetReason());
					}
				}
				else
				{
					$result_master->SetSuccessFlag(false);
					$result_master->SetReason($insert_result->GetReason());
				}
			}
			else
			{
				$result_master->SetSuccessFlag(true);
				$result_master->SetReason('Already In database');
				$result_master->SetPayload(-1);	
			}
		}
		else
		{
			$result_master->SetSuccessFlag(false);
			$result_master->SetReason($result->GetReason());
		}

		return $result_master;
	} 

	private function insert_search_location($location)
	{
		$result = new DatabaseResult(false, 'Have not checked db yet');
		$con = $this->OpenConnection();

		$query = "insert into search_locations (`location`) values (?)";

		//Prepare a statement
		$stmt = mysqli_stmt_init($con);

		if(mysqli_stmt_prepare($stmt, $query))
		{
			$stmt->bind_param('s', $location);

			if($stmt->execute())
			{
				$result->SetSuccessFlag(true);
				$result->SetReason('Successfully inserted');
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

	private function CheckDatabaseForSearchLocation($location)
	{
		$result = new DatabaseResult(false, 'Have not checked db yet');
		$con = $this->OpenConnection();

		$query = "select idsearch_locations from search_locations where location = ?";

		//Prepare a statement
		$stmt = mysqli_stmt_init($con);

		if(mysqli_stmt_prepare($stmt, $query))
		{
			$stmt->bind_param('s', $location);

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
					
					if(count($rows) == 0)
					{
						$result->SetSuccessFlag(true);
						$result->SetPayload(-1);
					}
					else
					{
						//database already had this entry
						$result->SetSuccessFlag(true);
						$result->SetPayload($rows[0][0]);
					}

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

	function ToString()
	{
		echo "server: $this->server<br/>";
		echo "database: $this->database<br/>";
		echo "username: $this->username<br/>";
		echo "password: $this->password<br/>";
	}

	function RetrieveSharedListings($size = 200)
	{
		$result = new DatabaseResult(false, 'Have not checked db yet');
		$con = $this->OpenConnection();

		$query = "select * from clbase_development.listings where `deleted` <> 1 AND `shared` = 1 order by `listing_date` desc limit ?";

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
						$listing = $this->ExtractRowDetailsIntoListingObject($row);
						
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

	private function ExtractRowDetailsIntoListingObject($row)
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
		$listing->SetSharedFlag($row[13]);

		return $listing;
	}

	function RetrieveListings($size = 200)
	{
		$result = new DatabaseResult(false, 'Have not checked db yet');
		$con = $this->OpenConnection();

		$query = "select * FROM clbase_development.listings where `deleted` <> 1 AND `shared` = 0 order by `listing_date` desc limit ?";

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
						$listing = $this->ExtractRowDetailsIntoListingObject($row);

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

		$query = "select * FROM clbase_development.listings where `favorite` = 1 AND `deleted` = 0 order by `listing_date` desc";

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
						$listing = $this->ExtractRowDetailsIntoListingObject($row);

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

	function DeleteListing($id)
	{
		//Check and see if there is a listing with that id
		$favorite_state_result = $this->GetFavoriteState($id);
		$result = new DatabaseResult(false, 'did nothing yet');

		if($favorite_state_result->GetSuccessFlag())
		{
			// $query = 
			// 	"delete from listings where `id` = ?";
			$query = 
				"update listings set `deleted` = 1 where `id` = ?";

			$con = $this->OpenConnection();

			$stmt = mysqli_stmt_init($con);

			if(mysqli_stmt_prepare($stmt, $query))
			{
				$stmt->bind_param('i', $id);

				if($stmt->execute())
				{
					$result->SetReason('listing deleted successfully');
					$result->SetSuccessFlag(true);
				}
				else
				{
					$result->SetSuccessFlag(false);
					$result->SetReason("error executing query: $query");
				}
			}
			else
			{
				$result->SetSuccessFlag(false);
				$result->SetReason("error preparing statement in DeleteListing()");
			}

			$this->CloseConnection($con);
		}
		else
		{
			$result->SetSuccessFlag(false);
			$result->SetReason("Could not find listing with id = $id");
		}

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