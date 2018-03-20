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
		<div class="container">
            <div class="row">
            <div class="col-lg-12 text-center">
			<br>
			<h2 class="section-heading text-uppercase">Login</h2>
            <form action="?redirect=' . $_GET['redirect'] . '" method="post">
                <table align="center">
                    <tr>
					<br>
                        <td>Email:</td>
                        <td><input class="form-control" type="text" name="email" value="' . (isset($email) ? $email : '') . '"></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input class="form-control" type="password" name="password"></td>
                    </tr>
                    <tr>
					<td colspan=2 align="right"><br> <button class="btn btn-primary btn-xl text-uppercase" type="submit" name="login" value="Login">Login</button></td>
                    </tr>
					<tr>
					<td colspan=2 align="center"><br><a href=register.php>Have not registered?</a> </td>
                    </tr>
                </table>
            </form>
			</div>
			</div>
			</div>
        </fieldset>
    ';
}

include 'footer.php';

?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
    <script src="bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>
	
        <meta charset="UTF-8">
        <title>Login Page</title>
		
	<!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom fonts for this template -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
    <link href='https://fonts.googleapis.com/css?family=Kaushan+Script' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Droid+Serif:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700' rel='stylesheet' type='text/css'>

    <!-- Custom styles for this template -->
    <link href="css/crowdfunding.min.css" rel="stylesheet">
    </head>
	
	<body>
	<!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Contact form JavaScript -->
    <script src="js/jqBootstrapValidation.js"></script>
    <script src="js/contact_me.js"></script>

    <!-- Custom scripts for this template -->
    <script src="js/crowdfunding.min.js"></script>
	</body>
</html>
