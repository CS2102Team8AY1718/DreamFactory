<!DOCTYPE html>
<html lang="en-US">
    <head>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
    <script src="bootstrap-4.0.0-dist/js/bootstrap.min.js"></script>
	
        <meta charset="UTF-8">
        <title>Registration Page</title>
		
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
            <div class="container">
            <div class="row">
            <div class="col-lg-12 text-center">
			<br>
			<h2 class="section-heading text-uppercase">Register</h2>
            <form action="?" method="post">
                <table align="center">
                    <tr>
					<br>
                        <td>Email:</td>
                        <td><input class="form-control" id="name" type="text" name="email" value="' . (isset($email) ? $email : '') . '"></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input class="form-control" id="name" type="password" name="password"></td>
                    </tr>
                    <tr>
                        <td>Re-enter Password:</td>
                        <td><input class="form-control" id="name" type="password" name = "confirm_password"></td>
                    </tr>
                    <tr>
                        <td>Full Name:</td>
                        <td><input class="form-control" id="name" type="text" name ="full_name" value="' . (isset($full_name) ? $full_name : '') . '"></td>
                    </tr>
                    <tr>
                        <td colspan=2 align="right"><br> <button id="sendMessageButton" class="btn btn-primary btn-xl text-uppercase" type="submit" name="register" value="Register">Register</button></td>
                    </tr>
                </table>
				<br>
            </form>
			</div>
			</div>
			</div>
        </fieldset>';
}

include 'footer.php';

?>
