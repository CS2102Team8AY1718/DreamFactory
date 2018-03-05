<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>Registration Page</title>
    </head>
</html>

<?php

require 'connect.php';

if (isset($_POST['register'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);

    $error_message = "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message .= "Please enter a valid email.<br>";
    }

    if (empty($password)) {
        $error_message .= "Please enter a password.<br>";
    }

    if ($password != $confirm_password) {
        $error_message .= "Confirmed password did not match.<br>";
    }

    if (empty($full_name)) {
        $error_message .= "Please enter your full name.<br>";
    }

    if (empty($error_message)) {
        // Calculate password_hash (SHA256 to the original password)
        $password_hash = hash('sha256', $password);

        // Insert new user into db
        $sql_insert_user = "INSERT INTO users (email, password_hash, full_name) VALUES ('$email', '$password_hash', '$full_name')";

        // Check for sql error
        if ($conn->query($sql_insert_user)) {
            echo "You have been registered. You can now <a href='login.php?redirect=homepage.php'>login</a>.";
            $retry = false;
        } else {
            echo "An account with that email has been found. Have you <a href='forgot_password.php'>forgotten your password</a>?";
            $retry = true;
        }
    } else {
        echo $error_message;
        $retry = true;
    }
}

if (!isset($retry) || $retry) {
    echo '
        <fieldset>
            <legend align="center">Register</legend>
            <form action="?" method="post">
                <table align="center">
                    <tr>
                        <td>Email:</td>
                        <td><input type="text" name="email" value="' . (isset($email) ? $email : '') . '"></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input type="password" name="password"></td>
                    </tr>
                    <tr>
                        <td>Re-enter Password:</td>
                        <td><input type="password" name = "confirm_password"></td>
                    </tr>
                    <tr>
                        <td>Full Name:</td>
                        <td><input type="text" name ="full_name" value="' . (isset($full_name) ? $full_name : '') . '"></td>
                    </tr>
                    <tr>
                        <td colspan=2 align="right"><input type="submit" name="register" value="Register"></td>
                    </tr>
                </table>
            </form>
        </fieldset>';
}

include 'footer.php';

?>
