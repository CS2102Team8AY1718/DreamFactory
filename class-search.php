<?php

class search {

  private $mysqli;
  
  public function __construct() {
    // Connect to our database and store in $mysqli property
    $this->connect();
  }
 
  private function connect() {
    $this->mysqli = new mysqli( 'localhost', 'root', 'password', 'dreamfactory' );
  }
  
  //Performs a search
  public function search($search_term) {
    $keyword = $this->mysqli->real_escape_string($search_term);
    
    // Run the query
    $query = $this->mysqli->query("
      SELECT title
      FROM project_keywords pk NATURAL JOIN projects p
      WHERE keyword LIKE '%{$keyword}%' AND p.project_id = pk.project_id
    ");
    
    // Check results
    if ( ! $query->num_rows ) {
      return false;
    }
    
    // Loop and fetch objects
    while( $row = $query->fetch_object() ) {
      $rows[] = $row;
    }
    
    // Build our return result
    $search_results = array(
      'count' => $query->num_rows,
      'results' => $rows,
    );
    
    return $search_results;
  }
}
?>

