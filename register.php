<?php
require 'connect.php';
session_start();

//$_SESSION['email'] = mysqli_real_escape_string($conn, $_POST['email']);
//$_SESSION['password'] = mysqli_real_escape_string($conn, $_POST['password']);
//$_SESSION['fullname'] = mysqli_real_escape_string($conn, $_POST['fullname']);

$email = $_POST['email'];
$password = $_POST['password'];
$fullname = $_POST['fullname'];

//after sending otp update database to store otp in password
$sql = "INSERT INTO users (email, password_hash, fullname) VALUES ('$email', '$password', '$fullname')";

if ($conn-> query($sql) == TRUE) {
	echo "yay";
} else {
	echo "Error: " . $sql . "<br>" . $conn->error;
}

?>
