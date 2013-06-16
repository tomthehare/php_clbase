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
}



?>