<?php
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
	echo $_SESSION['email'], ' is still logged in';
}else {
	echo 'You need to be logged in';
}

/*
 from https://www.youtube.com/watch?v=psed0RwsbB0
 to test session
*/
?>
