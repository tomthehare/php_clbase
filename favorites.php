<?php
  require_once("./databasemanager.php");
  require_once("./listing.php");

  $even = true;

  function echo_listing($listing)
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
          ."' id='".$listing->GetID()."' /></td>";
    echo "<td></td>";
    echo "</tr>";

    $even = !$even;
  }

?>

<!DOCTYPE html>
<html>
<head>
  <title>CLBASE - Craigslist Boston Area Search Engine</title>
  <style type='text/css'>
    @import "css/application.css";
  </style>
</head>
<body>
  <div id="banner">
    CLBASE
  </div>
  <div id="sub_banner">
    <a href="favorites.php">Favorites</a> | 
    <a href="listings.php">Recent Listings</a>
  </div>
  <div id="columns">
    <div id="main">
        <div id="listings_title">Favorite Boston Listings</div>

        <div class="listings_table">
        <table>
          <tr>
            <th>Title</th>
            <th>Listing date</th>
            <th>Price</th>
            <th>Bedroom count</th>
            <th>Location</th>
            <th>Image</th>
            <th></th> <!-- Favorite heading -->
            <th></th> <!-- Delete heading -->
          </tr>

        <?php
            //grab first 200 listings or something
            $dbmngr = new DatabaseManager();
            $result = $dbmngr->RetrieveFavoriteListings();

            if($result != null)
            {
              if($result->GetSuccessFlag())
              {
                $rows = $result->GetPayload();
                foreach($rows as $row)
                {
                  echo_listing($row);
                }
              }
            }

        ?>
        </table>
        </div>

        <br />

    </div>
  </div>

  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
  <script type='text/javascript'>

    $(".favorite_button").click(function() {
        var id = this.id;

        //ajax call using id
        $.ajax({
          type: "POST",
          url: "favoritemanager.php",
          data: {'lid':id},
          dataType: "json",
          success: function(data) {
            $(".favorite_button#" + id).val(data);
            //alert(data);
          },
          error: function(XMLHTTPRequest, textStatus, errorThrown) {
            alert('error: ' + XMLHTTPRequest.responseText);
          }

        });

      });
  </script>

</body>
</html>