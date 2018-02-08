<?php 
$servername="localhost";
$dbAdmin="root";
$password="";
$dbname="testdb";

//Create connection 
$conn=new mysqli($servername, $dbAdmin, $password,$dbname);

//check connection
if ($conn->connect_error){
		die("Connection failed: " . $conn->connect_error);
		echo "connection failed";
}else{
	echo "connected to db";
}
?>