<?php
//Check if search data was submitted
if ( isset( $_GET['s'] ) ) {
  // Include the search class
  require_once( dirname( __FILE__ ) . '/class-search.php' );
  
  // Instantiate a new instance of the search class
  $search = new search();
  
  // Store search term into a variable
  $search_term = htmlspecialchars($_GET['s'], ENT_QUOTES);
  
  // Send the search term to our search class and store the result
  $search_results = $search->search($search_term);
}
 ?> 
 
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>Search projects</title>
    </head>
    <body>
    <fieldset>
            <legend align="center">Browse project by keyword</legend>
      <form action="?" method="get">
	  <table align="center">
        <tr>
                        <td>Search:</td>
                        <td><input type="search" name="s" placeholder=" Enter keyword here" results="5">
                    </tr>
          <tr>
                        <td colspan=2 align="right"><input type="submit" name="search" value="Search"></td>
                    </tr>
		</table>
      </form>
	  </fieldset>
    </div>
	
<?php
if ( $search_results ) : ?>
    <div class="results-count">
      <p><?php echo $search_results['count']; ?> results found</p>
    </div>
    <div class="results-table">
      <?php foreach ( $search_results['results'] as $search_result ) : ?>
      <div class="result">
        <p><?php echo $search_result->title; ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="search-raw">
      <pre><?php  //print_r($search_results); // check results ?> </pre>
    </div>
    <?php endif; ?>
  </body>
</html>
	