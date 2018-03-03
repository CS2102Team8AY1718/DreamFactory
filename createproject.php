<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>Create Project</title>
    </head>
    <body>
<?php
	
require 'connect.php';
session_start();

if(isset($_SESSION['logged_in'])){
	if($_SESSION['logged_in'] ==true){
	 $logged_in = true;
	} else {
		$logged_in = false;
	}
}else{
	$logged_in=false;
}
	
$sql_select_user_id= "Select user_id from users where email='&_SESSION['email']'";	
if($logged_in ==true){
	echo 'Logged in as ', $_SESSION['email'];
	
	$result = $conn->query($sql_select_user_id);
}else {
	echo 'You need to be logged in.';
	echo '<a href="login.php">Login</a>';
}

// Check for sql error
if ($result = $conn->query($sql_select_user_id)) {
// Check if user is found
	if ($result->num_rows != 0) {
		$row = $result->fetch_assoc();
	}else{
		echo 'Error occured. Please check if you are logged in.<br>';
	}
}else{
}



  echo '
        <fieldset>
            <legend align="center">Create Project</legend>
            <form action="?" method="post">
                <table align="center">
                    <tr>
                        <td>Title</td>
                        <td><input type="text" name="title"></td>
                    </tr>
                    <tr>
                        <td>Image URL</td>
                        <td><input type="text" name="image"></td>
                    </tr>
					<tr>
						<td>Description</td>
                        <td><input type="text" name="description"></td>
					</tr>
					<tr>
						<td>End Date</td>
                        <td><input type="date" name="enddate"></td>
					</tr>
					<tr>
						<td>Category</td>
						<td><input type="text" name="category"></td>
					</tr>
					<tr>
						<td>Funding Goal</td>
						<td><input type="number" name="goal"></td>
					</tr>
					<tr>
						<td>Keyword</td>
						<td><input type="text" name="keyword"></td>
					</tr>
                    <tr>
                        <td colspan=2 align="right"><input type="submit" name="createproj" value="Create Project"></td>
                    </tr>
                </table>
            </form>
        </fieldset>';	
?>

<?php
$error_message="";
if(isset($_POST['createproj'])){
	$title= mysqli_real_escape_string($conn, $_POST['title']);
	$imgurl=mysqli_real_escape_string($conn, $_POST['image']);
	$description=mysqli_real_escape_string($conn, $_POST['description']);
	$enddate=$_POST["enddate"];
	$category=mysqli_real_escape_string($conn, $_POST['category']);
	$goal=$_POST['goal'];
	$keyword=trim(mysqli_real_escape_string($conn, $_POST['keyword']));
	$email=$_SESSION['email'];
	
	//get user_id
	$sql_select_userid = "SELECT user_id FROM users WHERE email='$email'";
	if($result= $conn->query($sql_select_userid)){
		$row=$result->fetch_assoc();
		$user_id=$row['user_id'];
		//create category no check
		$sql_create_category="Insert into categories (category) values ('$category')";
		$conn->query($sql_create_category);
		//create project
		$sql_create_project="Insert into projects (project_id, user_id, title, image_url, description,end_datetime,category,funding_goal)
								values (NULL, '$user_id', '$title', '$imgurl','$description','$enddate','$category','$goal')";
		if($conn->query($sql_create_project)){
			$error_message=$conn->query($sql_create_project);
			$error_message="Successfully created project";
		}else{
			$error_message=$conn->error;
		}
		
	}else{
		$error_message="Failed to get user id. Please check if you are logged in";
	}
	
}else{
	$error_message="Please fill in all the fields";
}

echo $error_message;
?>
	</body>
</html>