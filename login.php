<?php

require 'connect.php';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $error_message = "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message .= "Please enter a valid email.<br>";
    }

    if (empty($password)) {
        $error_message .= "Please enter your password.<br>";
    }

    if (empty($error_message)) {
        // Calculate password_hash (SHA256 to the original password)
        $password_hash = hash('sha256', $password);

        $sql_select_user = "SELECT user_id, email, full_name FROM users WHERE email='$email' AND password_hash='$password_hash'";

        // Check for sql error
        if ($result = $conn->query($sql_select_user)) {
            // Check if user is found
            if ($result->num_rows != 0) {
                $row = $result->fetch_assoc();

                // Login successful, initialize session information
                session_start();
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['full_name'] = $row['full_name'];

                header("Location: " . $_GET['redirect'] . "?logged_in");

                $retry = false;
            } else {
                echo "Invalid email and password combination.";
                $retry = true;
            }

            $result->free();
        } else {
            echo "Error: " . $sql_insert_user . "<br>" . $conn->error;
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
            <legend align="center">Login</legend>
            <form action="?redirect=' . $_GET['redirect'] . '" method="post">
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
                        <td colspan=2 align="right"><input type="submit" name="login" value="Login"></td>
                    </tr>
                </table>
            </form>
        </fieldset>
    ';
}

include 'footer.php';

?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>Login Page</title>
    </head>
</html>
