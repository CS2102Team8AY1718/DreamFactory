<?php
require 'connect.php';
session_start();
$_SESSION['user_name']=mysqli_real_escape_string($conn, $_POST['username']);
$_SESSION['user_password']=mysqli_real_escape_string($conn, $_POST['password']);

$sql="SELECT username, password FROM userbase WHERE username = '$_SESSION[user_name]'";
$result = $conn->query($sql);

if ($result->num_rows>0){
	$row=$result->fetch_assoc();  //rows fetched from db
	if($_SESSION['user_name'] ==$row["username"]){//check if user exists
	//	if($_SESSION['user_password'] ==$row["password"])
			header("Location:OTPpage.html");
		
		//else{
		//	echo "wrong password";
		//}
	//} else{
		//echo "wrong username";
	//}
	
}else{
	echo "no records found";
}
}
?>