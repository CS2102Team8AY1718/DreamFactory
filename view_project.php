<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
    <script src="bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>DreamFactory: Where Ideas Become Reality</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700' rel='stylesheet' type='text/css'>

    <!-- Custom styles for this template -->
    <link href="css/crowdfunding.min.css" rel="stylesheet">
</head>
<body id="page-top">
    <?php
        include 'header.php';
    ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color:Black;" id="mainNav">
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="#page-top">DreamFactory</a>
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                Menu
                <i class="fa fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav text-uppercase ml-auto">
                  <li class="nav-item">
                      <a class="nav-link js-scroll-trigger" href="homepage.php">Home</a>
                  </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="browse_projects.php">Projects</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<section class="bg-light">
<?php
session_start();
require 'connect.php';

    if(isset($_GET["project_id"]))
    {
        $project_id = $_GET["project_id"];
        $sql = "SELECT u.full_name, p.title, p.image_url, p.description, p.end_datetime, p.category, p.funding_goal, SUM(f.amount) as total from projects p natural join users u inner join fundings f on p.project_id = f.project_id where p.project_id='$project_id'";
		//check sql for error
		if ($result = $conn->query($sql)){
			//Check result is not null and store vars
			if ($result->num_rows != 0){
				$row = $result->fetch_assoc();
				$full_name=$row['full_name'];
				$title=$row['title'];
				$image_url=$row['image_url'];
				$description=$row['description'];
				$end_datetime=$row['end_datetime'];
				$category=$row['category'];
				$funding_goal=$row['funding_goal'];
				$total=$row['total'];
			}else{
				echo "project does not exist";
			}
		}else{
			echo "An sql error has occurred";
		}
    }


	echo '   <div class="container">
              <div class="row">
                  <div class="col-lg-12 text-center">
                  <img class="img-fluid" src="'.$image_url.'" style="max-height: 200px; width: auto;">
                  <br>
                  <br>
                      <h2 class="section-heading text-uppercase">' . $title .'</h2>
                      <p><i>' . $description .'</i></p>
                      <h6 class="text-muted">Owner:  <font face="roboto slab" size="3"  color="black"> ' . $full_name .' </font> </h6>
                      <h6 class="text-muted">Category:  <font face="roboto slab" size="3"  color="black"> ' . $category .' </font> </h6>
                      <h6 class="text-muted">Funding Amount:  <font face="roboto slab" size="3"  color="black">$ ';

                      if ($total > 0)
                      echo '' . $total .'';
                      else echo '0 ';

                      echo '/ '. $funding_goal .'</font> </h6>
                      <h6 class="text-muted">Project Deadline:  <font face="roboto slab" size="3"  color="black"> ' . $end_datetime .'</font> </h6>
                      </div>
                  </div>
              </div>
          ';

?>
</section>

<!-- Bootstrap core JavaScript -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Plugin JavaScript -->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Contact form JavaScript -->
<script src="js/jqBootstrapValidation.js"></script>
<script src="js/contact_me.js"></script>

<!-- Custom scripts for this template -->
<script src="js/crowdfunding.min.js"></script>
</body>
</html>
