<?php
require 'connect.php';
session_start();

$_SESSION['user_name'] = mysqli_real_escape_string($conn, $_POST['username']);
$_SESSION['user_password'] = mysqli_real_escape_string($conn, $_POST['password']);
$_SESSION['user_ptype'] = mysqli_real_escape_string($conn, $_POST['ptype']);

$sql = "INSERT INTO users (user_name, user_password, user_ptype)
VALUES ('$_SESSION[user_name]', '$_SESSION[user_password]', '$_SESSION[user_ptype]')";

$_SESSION['user_id'] = $conn->insert_id;

if ($conn->query($sql) === TRUE) {
    header("Location:list.html");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>