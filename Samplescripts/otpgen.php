<?php
require 'connect.php';
session_start();
//$_SESSION['user_name']=mysqli_real_escape_string($conn, $_POST['username']);


$bytes = openssl_random_pseudo_bytes(5, $cstrong);
$hex =bin2hex($bytes); 
echo $hex."\n";
$parameters=array(
'to'=>'6597715960',
'content'=>$hex //need to get from db
);

$url ='https://platform.clickatell.com/messages/http/send?apiKey=XWcfgiW6RuuVkziZw0GypA==&'.http_build_query($parameters);

//finalurl=http_build_query($url);
echo $url;

$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_exec($ch);

$hex=mysqli_real_escape_string($conn,$hex);
//after sending otp update database to store otp in password
$sql= "UPDATE userbase SET password='$hex' WHERE username ='$_SESSION[user_name]'";
//when user inputs otp in field call another php script :(((( and check if it matches. If ok then success. 
//To link to si hao, username login the fingerprint will trigger (master password?) to be
if ($conn-> query($sql) == TRUE) {
	echo "yay";
	header("Location:OTPpage.html");
} else {
	echo "Error: " . $sql . "<br>" . $conn->error;
}
?>
