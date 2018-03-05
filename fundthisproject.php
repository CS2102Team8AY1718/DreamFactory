<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>Login Page</title>
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

if($logged_in ==true){
	echo 'Logged in as ',$_SESSION['email'];
	//query for initial
	$sqlgetprojects = "select * from projects";
	$result = $conn->query($sqlgetprojects);

	if($result->num_rows !=0){
	echo'<fieldset>
		<legend align="center">Fund project</legend>
		<form action ="?" method="post">
			<table align="center">
			<tr>
			<td>Title</td>
			<td>';
	echo"<select name='title' id='title'>";
	while($row=$result->fetch_assoc()){
		echo "<option value='" . $row['title'] . "'>" . $row['title'] . "</option>";
	}
	echo '</select>
			<tr>
				<td colspan=2 align="right"><input type="submit" name="update" value="Get info"></td>
            </tr>
		</form>';
	}else{
	echo 'error occurred';
	}
	
	if (isset($_POST['update'])){
		$title=$_POST['title'];
		$sqlgetspecproject = "select * from projects where title='$title'";
		$result = $conn->query($sqlgetspecproject);
		$row = $result->fetch_assoc();
		echo '</td>';
		echo '
				<form action ="?" method="post">
				<tr>
					<td>Description</td>
					<td><input type="text" name="description" value="'.$row['description'].'"/ disabled></td>
				</tr>
				<tr>
					<td>Project ID</td>
					<td><input type="text" name="project_id" value="'.$row['project_id'].'"/ disabled></td>
				</tr>
				<tr>
					<td>Fund($)</td>
					<td><input type="text" name="fundamt"></td>
				</tr>
					<td colspan=2 align="right"><input type="submit" name="fund" value="Update Funding"></td>
				</form>
				</table>
			</fieldset>
			';
			
			if(isset($_POST['fund'])){
				$proj_id=$_POST['project_id'];
				$fundamt=$_POST['fundamt'];
				//get user_id
				$sql_select_userid = "SELECT user_id FROM users WHERE email='$_SESSION['email']'";
				$result= $conn->query($sql_select_userid);
				$row=$result->fetch_assoc();
				$user_id=$row['user_id'];
				//check if user has funded the project before
				$sql_check_for_history= "Select * from fundings where user_id='$user_id' and project_id='$proj_id'";
				$result= $conn->query($sql_check_for_history);
				if($result->num_rows!=0){ //has previously donated
					$sql_updatefund="Update fundings SET amount = '222', timestamp=CURRENT_TIMESTAMP where user_id='1' and project_id='2'";
					if($conn->query($sql_updatefund)){
						echo "update successful ";
					}else{
						echo "update failed ";
					}
				}else{ //never donated
					$sql_insertfund="INSERT into fundings (user_id, project_id, amount, timestamp) VALUES ('1','1','2', CURRENT_TIMESTAMP)";
					if($conn->query($sql_insertfund){
						echo "Your pledge has been recorded ";
					}else{
						echo "Pledge failed ";
					}
				}
			
			}else{
				echo "Please fill up the form";
			}
	
	}else {
		echo "error orccured";
	}
	
}else{
	echo 'You need to be logged in to view the page <br>';
	echo '<a href="login.php">Login</a>';
}
?>
	
	</body>
</html>