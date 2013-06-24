<?php
  require_once("./databasemanager.php");
  require_once("./listingsmanager.php");
  require_once("./listing.php");
  require_once("./common_html.php");
  require_once("common.php");

  check_user_session();
?>

  <?php CommonHTML::get_header(); ?>

  <div id="columns">
    <div id="main">
        <div id="page_title">Recent Boston Listings</div>

        <?php
            ListingsManager::start_table();

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
       
            ListingsManager::end_table();
        ?>

        <br />

    </div>
  </div>

  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
  <script type='text/javascript' src='listings.js'></script>

<?php CommonHTML::get_footer(); ?>