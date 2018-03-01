<?php
session_start();
session_destroy();

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
	echo 'Logged in as: ',$_SESSION['fullname'];
}else {
	echo 'Logged out successfully';
	echo '<a href="login.php">Login</a>';
}
?>