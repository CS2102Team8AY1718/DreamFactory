<!DOCTYPE html>
<html lang="en-US">
    <head>
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
    <script src="bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>
	
        <meta charset="UTF-8">
        <title>Create Project</title>

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
	
	<body>
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

<?php

session_start();
require 'connect.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php?redirect=create_project.php");
}

if (isset($_POST['create'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $image_url = mysqli_real_escape_string($conn, $_POST['image_url']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $end_date = mysqli_real_escape_string($conn, $_POST["end_date"]);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $goal = $_POST['goal'];
    $keyword = trim(mysqli_real_escape_string($conn, $_POST['keyword']));
	$keywordarr= explode(",", $keyword);
    
    $error_message = "";

    if (empty($title)) {
        $error_message .= "Please enter your project title.<br>";
    }

    if (empty($description)) {
        $error_message .= "Please provide a description of your project.<br>";
    }

    if (empty($end_date)) {
        $error_message .= "Please select a funding deadline for your project.<br>";
    }

    if (empty($category)) {
        $error_message .= "Please select a category for your project.<br>";
    }

    if (empty($goal)) {
        $error_message .= "Please provide a funding goal for your project.<br>";
    }

    if (empty($error_message)) {
        // Get user_id from session info
        $user_id = $_SESSION['user_id'];
		
		

        //create project
        $sql_create_project = "INSERT INTO projects (user_id, title, image_url, description, end_datetime, category, funding_goal) VALUES ('$user_id', '$title', '$image_url', '$description', '$end_date', '$category', '$goal')";
		//add keywords to keywords table
		$sql_insert_keyword = "INSERT INTO keywords (keyword) values ('$insertKeyword')";
		$sql_insert_into_project_keywords = "INSERT INTO project_keywords(project_id,keyword) VALUES ('$pid','$insertKeyword)'";
		$sql_get_pid ="SELECT project_id as from projects p where title='$title' and user_id='$user_id'";
								
        if ($conn->query($sql_create_project)){
			echo "Successfully created project! Go back to <a href='homepage.php'>homepage</a>.";
			//get the new pid
			if($result = $conn ->query($sql_get_pid)){
				$row  = $result->fetch_assoc();
				$pid = $row['project_id'];
			}
			foreach ($keywordarr as $insertKeyword){
				if($conn ->query($sql_insert_keyword){
					echo "Successfully added keywords";
					
				}else{
					//keyword exists
				}
				if($conn->query($sql_insert_into_project_keywords){
					echo "Your project is searchable! Go back to <a href='homepage.php'>homepage</a>.";
				}else{
					echo "error occurred";
				}
			}
            $retry = false;
        } else {
            echo $conn->error;
            echo "A project with the same title already exists. Please provide a different title.";
            $retry = true;
        }
    } else {
        echo $error_message;
        $retry = true;
    }
}

if (!isset($retry) || $retry) {
    $sql_select_categories = "SELECT * FROM categories";
    if ($result = $conn->query($sql_select_categories)) {
        $category_list = '';
        while ($row = $result->fetch_assoc()) {
            $category_list .= '<option value="' . $row['category'] . '">' . $row['category'] . '</option>';
        }
    }

    echo '
        <fieldset>
            <div class="container">
            <div class="row">
            <div class="col-lg-12 text-center">
			<br>
			<h2 class="section-heading text-uppercase">Create Project</h2>
            <form action="?" method="post">
                <table align="center">
				<br>
                    <tr>
                        <td>Title:</td>
                        <td><input class="form-control" id="name" type="text" name="title" value="' . (isset($title) ? $title : '') . '"></td>
                    </tr>
                    <tr>
                        <td>Image URL:</td>
                        <td><input class="form-control" id="name" type="text" name="image_url" value="' . (isset($image_url) ? $image_url : '') . '"></td>
                    </tr>
                    <tr>
                        <td>Description:</td>
                        <td><input class="form-control" id="name" type="text" name="description" value="' . (isset($description) ? $description : '') . '"></td>
                    </tr>
                    <tr>
                        <td>End Date:</td>
                        <td><input class="form-control" id="name" type="date" name="end_date" value="' . (isset($end_date) ? $end_date : '') . '"></td>
                    </tr>
                    <tr>
                        <td>Category:</td>
                        <td>
                            <select class="form-control" id="name" name="category">'
                            . $category_list .
                            '</select>
                        </td>
                    </tr>
                    <tr>
                        <td>Funding Goal ($):</td>
                        <td><input class="form-control" id="name" type="number" name="goal" value="' . (isset($goal) ? $goal : '') . '"></td>
                    </tr>
                    <!--<tr>
                        <td>Keyword:</td>
                        <td><input class="form-control" id="name" type="text" name="keyword"></td>
                    </tr>-->
                    <tr>
                        <td colspan=2 align="right"><br> <button id="sendMessageButton" class="btn btn-primary btn-xl text-uppercase" type="submit" name="create" value="Create">Create</button></td>
                    </tr>
                </table>
            </form>
			</div>
			</div>
			</div>
        </fieldset>
    ';
}

?>
