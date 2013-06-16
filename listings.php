<?php
  require_once("./databasemanager.php");
  require_once("./listingsmanager.php");
  require_once("./listing.php");
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
        <div id="listings_title">Recent Boston Listings</div>

        <div class="listings_table">
        <table>
          <tr>
            <th>Title</th>
            <th>Listing date</th>
            <th>Price</th>
            <th>Bedroom count</th>
            <th>Location</th>
            <th>Image</th>
            <th>Fav</th> <!-- Favorite heading -->
            <th>Del</th> <!-- Delete heading -->
          </tr>

        <?php
            //grab first 200 listings or something
            $dbmngr = new DatabaseManager();
            $result = $dbmngr->RetrieveListings();

            if($result != null)
            {
              if($result->GetSuccessFlag())
              {
                $rows = $result->GetPayload();
                foreach($rows as $row)
                {
                  ListingsManager::echo_listing($row);
                }
              }
            }
        ?>
        </table>
        </div>

        <br />

    </div>
  </div>

  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
  <script type='text/javascript' src='listings.js'></script>
</body>
</html>