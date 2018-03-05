<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>Registration Page</title>
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
		
		if($logged_in==true){
			echo 'Logged in as ',$_SESSION['email'];
			echo	'
					<form action ="?" method="post">
					<tr>
						<td>Project ID</td>
						<td><input type="text" name="project_id"></td>
					</tr>
					<tr>
						<td>Fund($)</td>
						<td><input type="text" name="fundamt"></td>
					</tr>
						<td colspan=2 align="right"><input type="submit" name="fund"></td>
					</form>
					';
		}else{
			echo 'You need to be logged in to view the page <br>';
			echo '<a href="login.php">Login</a>';
		}
	?>
	<?php
		if(isset($_POST['fund'])){
				$proj_id=$_POST['project_id'];
				$fundamt=$_POST['fundamt'];
				$email=$_SESSION['email'];
				//get user_id
				$sql_select_userid = "SELECT user_id FROM users WHERE email='$email'";
				$result= $conn->query($sql_select_userid);
				$row=$result->fetch_assoc();
				$user_id=$row['user_id'];
				//check if user has funded the project before
				$sql_check_for_history= "Select * from fundings where user_id='$user_id' and project_id='$proj_id'";
				$result= $conn->query($sql_check_for_history);
				if($result->num_rows!=0){ //has previously donated
					$sql_updatefund="Update fundings SET amount = '$fundamt', timestamp=CURRENT_TIMESTAMP where user_id='$user_id' and project_id='$proj_id'";
					if($conn->query($sql_updatefund)){
						echo "update successful ";
					}else{
						echo "update failed ";
					}
				}else{ //never donated
					$sql_insertfund="INSERT into fundings (user_id, project_id, amount, timestamp) VALUES ('$user_id','$proj_id','$fundamt', CURRENT_TIMESTAMP)";
					if($conn->query($sql_insertfund)){
						echo 'Your pledge has been recorded ';
					}else{
						echo 'Pledge failed ';
					}
				}
			
			}else{
				echo 'Please fill up the form';
			}
	?>
	</body>
</html>