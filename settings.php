<?php //settings

require_once('./common_html.php');
require_once('./databasemanager.php');
require_once("common.php");

check_user_session();

CommonHTML::get_header();
?>

<div id='page_title'>Settings</div>
<p>
<?php

	$dbmngr = new DatabaseManager();
	$result = $dbmngr->GetSearchLocations();

	$locations = $result->GetPayload();

	if($result->GetSuccessFlag() && count($locations) > 0)
	{
		echo "<div class='listings_table'>";
		echo "<table id='settings_table'>
				<th>Search Location</th>
				<th>Delete</th>";

		$even = true;

		$keys = array_keys($locations);
		foreach($keys as $key)
		{

			echo "<tr id='row_",$key,"'>";
			echo "<td>", $locations[$key], "</td>";
			echo "<td><input type='button' class='del_btn' id='del_",$key,"' value='Delete' /></td>";
			echo "</tr>";
		}

		echo "</table></div>";
	}
	else
	{
		echo "Nothing found";
	}

	//do a submit location here
	echo "<p>";
	echo "<input type='text' placeholder='Add new search term' id='new_search_term_txt' />";
	echo "<input type='button' value='Submit' id='new_search_term_btn' />";

?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type='text/javascript' src='settings.js'></script>

<?php CommonHTML::get_footer(); ?>