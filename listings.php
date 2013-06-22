<?php
  require_once("./databasemanager.php");
  require_once("./listingsmanager.php");
  require_once("./listing.php");
  require_once("./common_html.php");
?>

  <?php CommonHTML::get_header(); ?>

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
            $result = $dbmngr->RetrieveListings(1000);

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

<?php CommonHTML::get_footer(); ?>