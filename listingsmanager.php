<?php

class ListingsManager
{
	private static $even = true;

	public static function echo_listing($listing)
	{
		global $even;

		echo "<tr class='". ($even ? "list_line_even" : "list_line_odd") ."'>";
		echo "<td>
		      <div id='listing_link'>
		      <a href='".$listing->GetLink()."'>".$listing->GetTitle()."</a>
		      </div>
		      </td>";
		echo "<td>"
		      .$listing->GetDateIntervalFromToday()->d." days ago - "
		      ."</td>";
		echo "<td>".$listing->GetPrice()."</td>";
		echo "<td>".$listing->GetBedroomCount()."</td>";
		echo "<td>".$listing->GetLocation()."</td>";
		echo "<td>".$listing->GetImageFlag()."</td>";

		echo "<td><input type='button' class='favorite_button' value='"
		      .($listing->GetFavoriteFlag() ? "Unfavorite" : "Favorite")
		      ."' id='fav_btn_".$listing->GetID()."' /></td>";
		echo "<td>
		      <input type='button' class='delete_button' value='Delete' id='del_btn_".$listing->GetID()."' />
		      </td>";
		echo "</tr>";

		$even = !$even;
	}

	public static function start_table()
	{
		echo "<div class='listings_table'>
        <table>
          <tr>
            <th>Title</th>
            <th>Listing date</th>
            <th>Price</th>
            <th>Bedroom count</th>
            <th>Location</th>
            <th>Image</th>
            <th>Fav</th> 
            <th>Del</th>
          </tr>";
	}

	public static function end_table()
	{
		echo "</table>
        </div>";
	}
}



?>